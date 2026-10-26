<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\AutoTranslateService;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\I18n\I18n;
use Cake\ORM\TableRegistry;

/**
 * TranslateContent command.
 *
 * Traduce in batch in inglese (GoogleTranslate, stesso AutoTranslateService usato dopo le
 * generazioni AI) la descrizione o i campi SEO di Poi, Percorsi e/o Destinations, senza
 * rigenerare nulla. Stesse risorse/filtri di regenerate_ai_content.
 *
 * Come regenerate_ai_content va lanciato con HTTP_HOST (config per-sito):
 *
 *   HTTP_HOST=l.ebike.bikesquare.eu bin/cake translate_content --type=description
 *
 * Uso:
 *   bin/cake translate_content --type=description                          # risorse pubblicate senza traduzione eng
 *   bin/cake translate_content --type=seo --model=Percorsi --destination=1,2
 *   bin/cake translate_content --type=description --model=Poi --id=11,22  # forza quei Poi anche se già tradotti
 *   bin/cake translate_content --type=description --force-override        # ritraduce TUTTE le risorse pubblicate
 *   bin/cake translate_content --type=description --dry-run
 */
class TranslateContentCommand extends Command
{
    public static function defaultName(): string
    {
        return 'translate_content';
    }

    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $targets = RegenerateAiContentCommand::TARGETS;
        $parser
            ->setDescription('Traduce in batch in inglese la descrizione o i campi SEO di Poi, Percorsi e/o Destinations.')
            ->addOption('type', [
                'required' => true,
                'choices' => ['seo', 'description'],
                'help' => 'Cosa tradurre: "seo" (seo_description/seo_keywords) oppure "description".',
            ])
            ->addOption('model', [
                'choices' => array_keys($targets),
                'help' => 'Tipo di risorsa: Poi, Percorsi o Destinations. Default: tutti e tre.',
            ])
            ->addOption('destination', [
                'help' => 'Id Destination separati da virgola: limita a risorse di quelle destinazioni. Default: tutte.',
            ])
            ->addOption('id', [
                'help' => 'Id risorsa separati da virgola: forza la traduzione anche se già presente. Richiede --model.',
            ])
            ->addOption('user', [
                'help' => 'Id utente da registrare in AutoTranslationHistory. Default: nessuno.',
            ])
            ->addOption('sleep', [
                'default' => '1',
                'help' => 'Secondi di pausa tra una traduzione e la successiva. Default: 1.',
            ])
            ->addOption('dry-run', [
                'boolean' => true,
                'default' => false,
                'help' => 'Mostra solo l\'elenco: nessuna traduzione, nessun salvataggio, nessuna conferma richiesta.',
            ])
            ->addOption('force-override', [
                'boolean' => true,
                'default' => false,
                'help' => 'Ignora il filtro "traduzione mancante": ritraduce anche le risorse già tradotte (rispetta --model/--destination e "published").',
            ])
            ->addOption('yes', [
                'short' => 'y',
                'boolean' => true,
                'default' => false,
                'help' => 'Salta la conferma interattiva (utile per cron/script).',
            ]);

        return $parser;
    }

    public function execute(Arguments $args, ConsoleIo $io): int
    {
        // Sorgente italiana dalla tabella principale (vedi nota in RegenerateAiContentCommand)
        I18n::setLocale('ita');

        $type = (string)$args->getOption('type');
        $modelOption = $args->getOption('model');
        $idOption = $args->getOption('id');
        $userId = $args->getOption('user') ? (int)$args->getOption('user') : null;
        $sleepSeconds = max(0, (int)$args->getOption('sleep'));
        $dryRun = (bool)$args->getOption('dry-run');
        $skipConfirm = (bool)$args->getOption('yes');
        $forceOverride = (bool)$args->getOption('force-override');

        if (!$dryRun && !AutoTranslateService::isConfigured()) {
            $io->error('GoogleTranslate.LANGUAGE_TRANSLATOR_APIKEY non configurata per questo sito.');
            $io->error('In CLI la config per-sito richiede HTTP_HOST: HTTP_HOST=<sito> bin/cake translate_content ...');
            return static::CODE_ERROR;
        }

        if ($idOption && !$modelOption) {
            $io->error('--id richiede --model (Poi, Percorsi o Destinations): un id da solo è ambiguo tra le tabelle.');
            return static::CODE_ERROR;
        }

        $ids = $this->parseIds($idOption);
        $destinationIds = $this->parseIds($args->getOption('destination'));
        if (($idOption && !$ids) || ($args->getOption('destination') && !$destinationIds)) {
            $io->error('Nessun id valido in --id/--destination.');
            return static::CODE_ERROR;
        }

        $targets = RegenerateAiContentCommand::TARGETS;
        $targetNames = $modelOption ? [$modelOption] : array_keys($targets);

        $jobs = [];
        foreach ($targetNames as $targetName) {
            $config = $targets[$targetName];
            $fields = $type === 'seo' ? RegenerateAiContentCommand::SEO_FIELDS : [$config['descriptionField']];

            $table = TableRegistry::getTableLocator()->get($config['table']);
            $alias = $table->getAlias();
            $sourceField = $fields[0];

            $query = $table->find('translations', ['locales' => ['eng']]);
            if ($ids !== null) {
                $query->where(["$alias.id IN" => $ids]);
            } else {
                $query->where([
                    "$alias.published" => 1,
                    "$alias.$sourceField IS NOT" => null,
                    "$alias.$sourceField !=" => '',
                ]);
            }
            if ($destinationIds !== null) {
                $query->where(["$alias.{$config['destinationField']} IN" => $destinationIds]);
            }

            foreach ($query->all() as $entity) {
                $eng = $entity->get('_translations')['eng'] ?? null;
                if ($ids === null && !$forceOverride && $eng && !empty($eng->get($sourceField))) {
                    continue;
                }
                $jobs[] = ['target' => $targetName, 'entity' => $entity, 'config' => $config, 'fields' => $fields];
            }
        }

        $total = count($jobs);
        $io->out("Tipo contenuto: <info>{$type}</info>");
        $io->out('Risorse: <info>' . implode(', ', $targetNames) . '</info>');
        if ($ids === null && $forceOverride) {
            $io->warning('Modalità: --force-override attivo, verranno ritradotte anche le risorse già tradotte.');
        }
        $io->out("Totale da tradurre: <info>{$total}</info>");
        $io->hr();

        if ($total === 0) {
            $io->success('Niente da fare.');
            return static::CODE_SUCCESS;
        }

        foreach ($jobs as $job) {
            $io->out("[{$job['target']}] #{$job['entity']->id} - {$job['entity']->{$job['config']['titleField']}}");
        }
        $io->hr();

        if ($dryRun) {
            $io->warning('Modalità DRY-RUN: elenco mostrato, nessuna traduzione né salvataggio.');
            return static::CODE_SUCCESS;
        }

        if (!$skipConfirm) {
            $confirm = $io->askChoice('Procedo?', ['y', 'n'], 'y');
            if (strtolower((string)$confirm) !== 'y') {
                $io->out('Annullato.');
                return static::CODE_SUCCESS;
            }
        }

        $translator = new AutoTranslateService();
        $ok = 0;
        $failed = 0;
        $lastIndex = $total - 1;

        foreach ($jobs as $i => $job) {
            $entity = $job['entity'];
            $io->out("[{$job['target']}] #{$entity->id} - {$entity->{$job['config']['titleField']}}");
            try {
                $translator->toEnglish(TableRegistry::getTableLocator()->get($job['config']['table']), $entity, $job['fields'], $userId);
                $io->out('  tradotto: ' . implode(', ', $job['fields']));
                $ok++;
            } catch (\Throwable $e) {
                $io->error('  Traduzione fallita: ' . $e->getMessage());
                $failed++;
            }

            if ($sleepSeconds > 0 && $i < $lastIndex) {
                sleep($sleepSeconds);
            }
        }

        $io->hr();
        $io->out("Completati: <info>{$ok}</info>, falliti: <info>{$failed}</info>");

        return $failed === 0 ? static::CODE_SUCCESS : static::CODE_ERROR;
    }

    private function parseIds(mixed $option): ?array
    {
        if (!$option) {
            return null;
        }

        return array_values(array_filter(array_map('intval', explode(',', (string)$option))));
    }
}
