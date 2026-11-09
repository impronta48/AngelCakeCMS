<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\DestinationSeoAiService;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;

/**
 * RegenerateDestinationSeo command.
 *
 * Rigenera seo_description/seo_keywords delle Destinations tramite AI (OpenRouter),
 * riusando lo stesso DestinationSeoAiService del bottone "Rigenera SEO con AI"
 * nell'admin. Speculare a Cyclomap\Command\RegeneratePoiSeoCommand.
 *
 * IMPORTANTE: la configurazione (OpenRouter.apiKey, ecc.) è per-sito e viene
 * risolta da conf_path() in base a $_SERVER['HTTP_HOST'] (config/paths.php),
 * che in CLI è sempre vuoto: senza HTTP_HOST il comando carica sites/default
 * invece di sites/l.ebike.bikesquare.eu e non trova la chiave. Va quindi
 * lanciato impostando l'host esplicitamente:
 *
 *   HTTP_HOST=l.ebike.bikesquare.eu bin/cake regenerate_destination_seo
 *
 * Uso:
 *   bin/cake regenerate_destination_seo                  # solo le Destinations senza seo_description
 *   bin/cake regenerate_destination_seo --ids=1,2,3       # forza la rigenerazione di quelle, anche se già compilate
 *   bin/cake regenerate_destination_seo --dry-run         # simula, non scrive nulla sul DB
 *   bin/cake regenerate_destination_seo --ids=1 --model=claude   # usa un modello diverso da quello di default
 */
class RegenerateDestinationSeoCommand extends Command
{
    protected $modelClass = 'Destinations';

    public static function defaultName(): string
    {
        return 'regenerate_destination_seo';
    }

    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser
            ->setDescription('Rigenera seo_description/seo_keywords delle Destinations tramite AI (OpenRouter).')
            ->addOption('ids', [
                'help' => 'Id Destinations separati da virgola: forza la rigenerazione anche se già compilate. Senza questa opzione vengono processate solo le Destinations con seo_description mancante.',
            ])
            ->addOption('dry-run', [
                'boolean' => true,
                'default' => false,
                'help' => 'Simula senza scrivere sul DB.',
            ])
            ->addOption('model', [
                'help' => 'Alias modello OpenRouter da usare (es. gemini, claude, opus). Default: AiRouter.default_model in config/airouter.php.',
            ]);

        return $parser;
    }

    public function execute(Arguments $args, ConsoleIo $io): int
    {
        if (empty(Configure::read('OpenRouter.apiKey'))) {
            $io->error('OpenRouter.apiKey non configurata per questo sito.');
            $io->error('In CLI la config per-sito richiede HTTP_HOST: HTTP_HOST=l.ebike.bikesquare.eu bin/cake regenerate_destination_seo ...');
            return static::CODE_ERROR;
        }

        $dryRun = (bool)$args->getOption('dry-run');
        $idsOption = $args->getOption('ids');
        $model = $args->getOption('model');

        $query = $this->Destinations->find();

        if ($idsOption) {
            $ids = array_values(array_filter(array_map('intval', explode(',', (string)$idsOption))));
            if (empty($ids)) {
                $io->error('Nessun id valido in --ids.');
                return static::CODE_ERROR;
            }
            $query->where(['Destinations.id IN' => $ids]);
            $io->out('Modalità: rigenerazione forzata per ' . count($ids) . ' Destinations specificate (anche se già compilate).');
        } else {
            $query->where(['OR' => [
                'Destinations.seo_description IS' => null,
                'Destinations.seo_description' => '',
            ]]);
            $io->out('Modalità: batch, solo Destinations con seo_description mancante.');
        }

        $destinations = $query->all();
        $total = $destinations->count();
        $io->out("Destinations da processare: <info>{$total}</info>");

        if ($total === 0) {
            $io->success('Niente da fare.');
            return static::CODE_SUCCESS;
        }

        if ($dryRun) {
            $io->warning('Modalità DRY-RUN: nessuna modifica verrà salvata.');
        }

        $seoService = new DestinationSeoAiService();
        $ok = 0;
        $failed = 0;

        foreach ($destinations as $destination) {
            $io->out("Destination #{$destination->id} - {$destination->name}");

            $seo = $seoService->generate($destination, $model ?: null);
            if (!$seo) {
                $io->error("  Generazione fallita per Destination #{$destination->id}, skip.");
                $failed++;
                continue;
            }

            $io->out("  seo_description: {$seo['seo_description']}");
            $io->out("  seo_keywords: {$seo['seo_keywords']}");

            if (!$dryRun) {
                $destination->seo_description = $seo['seo_description'];
                $destination->seo_keywords = $seo['seo_keywords'];
                if (!$this->Destinations->save($destination)) {
                    $io->error("  Salvataggio fallito per Destination #{$destination->id}.");
                    $failed++;
                    continue;
                }
            }

            $ok++;
        }

        $io->hr();
        $io->out("Completati: <info>{$ok}</info>, falliti: <info>{$failed}</info>");

        return $failed === 0 ? static::CODE_SUCCESS : static::CODE_ERROR;
    }
}
