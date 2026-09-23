<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\DestinationDescriptionAiService;
use App\Service\DestinationSeoAiService;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cyclomap\Service\PercorsoDescriptionAiService;
use Cyclomap\Service\PercorsoSeoAiService;
use Cyclomap\Service\PoiDescriptionAiService;
use Cyclomap\Service\PoiSeoAiService;

/**
 * RegenerateAiContent command.
 *
 * Genera in batch (via AI/OpenRouter) i campi SEO (seo_description/seo_keywords) o la
 * descrizione editoriale (descr/descrizione) di Poi, Percorsi e/o Destinations, riusando
 * gli stessi service dei bottoni "Rigenera... con AI" nell'admin.
 *
 * IMPORTANTE: la configurazione (OpenRouter.apiKey, ecc.) è per-sito e viene risolta da
 * conf_path() in base a $_SERVER['HTTP_HOST'] (config/paths.php), che in CLI è sempre
 * vuoto: senza HTTP_HOST il comando carica sites/default e non trova la chiave. Va quindi
 * lanciato impostando l'host esplicitamente:
 *
 *   HTTP_HOST=l.ebike.bikesquare.eu bin/cake regenerate_ai_content --type=seo
 *
 * NOTA: `descr` su Poi/Percorsi è un campo obbligatorio in validazione, quindi in pratica
 * quasi nessuna riga esistente lo ha vuoto — senza `--id`, un run con --type=description su
 * Poi/Percorsi troverà verosimilmente 0 risultati (non è un bug: usa --id per rigenerare
 * descrizioni già compilate).
 *
 * Uso:
 *   bin/cake regenerate_ai_content --type=seo                              # tutte le risorse pubblicate senza SEO
 *   bin/cake regenerate_ai_content --type=description --model=Destinations # solo Destinations, descrizione mancante
 *   bin/cake regenerate_ai_content --type=seo --model=Percorsi --destination=1,2
 *   bin/cake regenerate_ai_content --type=seo --model=Poi --id=11,22       # forza quei Poi anche se già compilati
 *   bin/cake regenerate_ai_content --type=seo --dry-run                    # mostra solo l'elenco
 *   bin/cake regenerate_ai_content --type=seo --ai-model=claude --sleep=5
 */
class RegenerateAiContentCommand extends Command
{
    /**
     * Mappa risorsa -> tabella/service/campi. 'contain' è l'unione di ciò che serve sia al
     * flusso SEO sia a quello descrizione, per non dover ramificare la query in base a --type.
     */
    private const TARGETS = [
        'Poi' => [
            'table' => 'Cyclomap.Poi',
            'contain' => ['Categorie', 'Destinations', 'Percorsi'],
            'seoService' => PoiSeoAiService::class,
            'descriptionService' => PoiDescriptionAiService::class,
            'descriptionField' => 'descr',
            'titleField' => 'title',
            'destinationField' => 'destination_id',
        ],
        'Percorsi' => [
            'table' => 'Cyclomap.Percorsi',
            'contain' => ['Destinations', 'Tipi', 'Filtri', 'Poi'],
            'seoService' => PercorsoSeoAiService::class,
            'descriptionService' => PercorsoDescriptionAiService::class,
            'descriptionField' => 'descr',
            'titleField' => 'title',
            'destinationField' => 'destination_id',
        ],
        'Destinations' => [
            'table' => 'Destinations',
            'contain' => [],
            'seoService' => DestinationSeoAiService::class,
            'descriptionService' => DestinationDescriptionAiService::class,
            'descriptionField' => 'descrizione',
            'titleField' => 'name',
            'destinationField' => 'id',
        ],
    ];

    public static function defaultName(): string
    {
        return 'regenerate_ai_content';
    }

    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser
            ->setDescription('Genera in batch (via AI/OpenRouter) i campi SEO o la descrizione editoriale di Poi, Percorsi e/o Destinations.')
            ->addOption('type', [
                'required' => true,
                'choices' => ['seo', 'description'],
                'help' => 'Cosa generare: "seo" (seo_description/seo_keywords) oppure "description" (corpo articolo/descrizione).',
            ])
            ->addOption('model', [
                'choices' => array_keys(self::TARGETS),
                'help' => 'Tipo di risorsa: Poi, Percorsi o Destinations. Default: tutti e tre.',
            ])
            ->addOption('destination', [
                'help' => 'Id Destination separati da virgola: limita a risorse di quelle destinazioni. Default: tutte.',
            ])
            ->addOption('id', [
                'help' => 'Id risorsa separati da virgola: forza la rigenerazione anche se già compilata. Richiede --model.',
            ])
            ->addOption('ai-model', [
                'help' => 'Alias modello OpenRouter da usare (es. gemini, claude, opus). Default: AiRouter.default_model.',
            ])
            ->addOption('sleep', [
                'default' => '2',
                'help' => 'Secondi di pausa tra una generazione e la successiva. Default: 2.',
            ])
            ->addOption('dry-run', [
                'boolean' => true,
                'default' => false,
                'help' => 'Mostra solo l\'elenco: nessuna chiamata AI, nessun salvataggio, nessuna conferma richiesta.',
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
        if (empty(Configure::read('OpenRouter.apiKey'))) {
            $io->error('OpenRouter.apiKey non configurata per questo sito.');
            $io->error('In CLI la config per-sito richiede HTTP_HOST: HTTP_HOST=<sito> bin/cake regenerate_ai_content ...');
            return static::CODE_ERROR;
        }

        $type = (string)$args->getOption('type');
        $modelOption = $args->getOption('model');
        $idOption = $args->getOption('id');
        $destinationOption = $args->getOption('destination');
        $aiModel = $args->getOption('ai-model') ?: null;
        $sleepSeconds = max(0, (int)$args->getOption('sleep'));
        $dryRun = (bool)$args->getOption('dry-run');
        $skipConfirm = (bool)$args->getOption('yes');

        if ($idOption && !$modelOption) {
            $io->error('--id richiede --model (Poi, Percorsi o Destinations): un id da solo è ambiguo tra le tabelle.');
            return static::CODE_ERROR;
        }

        $ids = null;
        if ($idOption) {
            $ids = array_values(array_filter(array_map('intval', explode(',', (string)$idOption))));
            if (empty($ids)) {
                $io->error('Nessun id valido in --id.');
                return static::CODE_ERROR;
            }
        }

        $destinationIds = null;
        if ($destinationOption) {
            $destinationIds = array_values(array_filter(array_map('intval', explode(',', (string)$destinationOption))));
            if (empty($destinationIds)) {
                $io->error('Nessun id valido in --destination.');
                return static::CODE_ERROR;
            }
        }

        $targetNames = $modelOption ? [$modelOption] : array_keys(self::TARGETS);

        $jobs = [];
        foreach ($targetNames as $targetName) {
            $config = self::TARGETS[$targetName];
            $table = TableRegistry::getTableLocator()->get($config['table']);
            $alias = $table->getAlias();

            $query = $table->find();
            if (!empty($config['contain'])) {
                $query->contain($config['contain']);
            }

            if ($ids !== null) {
                $query->where(["$alias.id IN" => $ids]);
            } else {
                $fieldToCheck = $type === 'seo' ? 'seo_description' : $config['descriptionField'];
                $query->where([
                    "$alias.published" => 1,
                    'OR' => [
                        "$alias.$fieldToCheck IS" => null,
                        "$alias.$fieldToCheck" => '',
                    ],
                ]);
            }

            if ($destinationIds !== null) {
                $query->where(["$alias.{$config['destinationField']} IN" => $destinationIds]);
            }

            foreach ($query->all() as $entity) {
                $jobs[] = ['target' => $targetName, 'entity' => $entity, 'config' => $config];
            }
        }

        $total = count($jobs);
        $io->out("Tipo contenuto: <info>{$type}</info>");
        $io->out('Risorse: <info>' . implode(', ', $targetNames) . '</info>');
        $io->out("Totale da processare: <info>{$total}</info>");
        $io->hr();

        if ($total === 0) {
            $io->success('Niente da fare.');
            return static::CODE_SUCCESS;
        }

        foreach ($jobs as $job) {
            $entity = $job['entity'];
            $titleField = $job['config']['titleField'];
            $io->out("[{$job['target']}] #{$entity->id} - {$entity->{$titleField}}");
        }
        $io->hr();

        if ($dryRun) {
            $io->warning('Modalità DRY-RUN: elenco mostrato, nessuna generazione né salvataggio.');
            return static::CODE_SUCCESS;
        }

        if (!$skipConfirm) {
            $confirm = $io->askChoice('Procedo?', ['y', 'n'], 'y');
            if (strtolower((string)$confirm) !== 'y') {
                $io->out('Annullato.');
                return static::CODE_SUCCESS;
            }
        }

        $ok = 0;
        $failed = 0;
        $lastIndex = count($jobs) - 1;

        foreach (array_values($jobs) as $i => $job) {
            $entity = $job['entity'];
            $config = $job['config'];
            $table = TableRegistry::getTableLocator()->get($config['table']);
            $titleField = $config['titleField'];

            $io->out("[{$job['target']}] #{$entity->id} - {$entity->{$titleField}}");

            if ($type === 'seo') {
                $service = new $config['seoService']();
                $result = $service->generate($entity, $aiModel);
                if (!$result) {
                    $io->error('  Generazione fallita, skip.');
                    $failed++;
                } else {
                    $entity->seo_description = $result['seo_description'];
                    $entity->seo_keywords = $result['seo_keywords'];
                    if ($table->save($entity)) {
                        $io->out("  seo_description: {$result['seo_description']}");
                        $ok++;
                    } else {
                        $io->error('  Salvataggio fallito.');
                        $failed++;
                    }
                }
            } else {
                $service = new $config['descriptionService']();
                $result = $service->generate($entity, $aiModel);
                if (!$result) {
                    $io->error('  Generazione fallita, skip.');
                    $failed++;
                } else {
                    $descField = $config['descriptionField'];
                    $entity->$descField = $result;
                    if ($table->save($entity)) {
                        $io->out("  {$descField} aggiornato.");
                        $ok++;
                    } else {
                        $io->error('  Salvataggio fallito.');
                        $failed++;
                    }
                }
            }

            if ($sleepSeconds > 0 && $i < $lastIndex) {
                sleep($sleepSeconds);
            }
        }

        $io->hr();
        $io->out("Completati: <info>{$ok}</info>, falliti: <info>{$failed}</info>");

        return $failed === 0 ? static::CODE_SUCCESS : static::CODE_ERROR;
    }
}
