<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\DestinationDescriptionAiService;
use App\Service\DestinationSeoAiService;
use App\Service\AutoTranslateService;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;
use Cake\I18n\I18n;
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
 * gli stessi service dei bottoni "Rigenera... con AI" nell'admin. Come nell'admin, dopo ogni
 * generazione il testo viene tradotto in inglese (AutoTranslateService), salvo --skip-translate.
 * Solo traduzione, senza AI: vedi translate_content.
 *
 * IMPORTANTE: la configurazione (OpenRouter.apiKey, ecc.) è per-sito e viene risolta da
 * conf_path() in base a $_SERVER['HTTP_HOST'] (config/paths.php), che in CLI è sempre
 * vuoto: senza HTTP_HOST il comando carica sites/default e non trova la chiave. Va quindi
 * lanciato impostando l'host esplicitamente:
 *
 *   HTTP_HOST=l.ebike.bikesquare.eu bin/cake regenerate_ai_content --type=seo
 *
 * NOTA: `descr` su Poi/Percorsi è un campo obbligatorio in validazione, quindi in pratica
 * quasi nessuna riga esistente lo ha vuoto — senza `--id`/`--force-override`, un run con
 * --type=description su Poi/Percorsi troverà verosimilmente 0 risultati (non è un bug: usa
 * --id o --force-override per rigenerare descrizioni già compilate).
 *
 * Uso:
 *   bin/cake regenerate_ai_content --type=seo                              # tutte le risorse pubblicate senza SEO
 *   bin/cake regenerate_ai_content --type=description --model=Destinations # solo Destinations, descrizione mancante
 *   bin/cake regenerate_ai_content --type=seo --model=Percorsi --destination=1,2
 *   bin/cake regenerate_ai_content --type=seo --model=Poi --id=11,22       # forza quei Poi anche se già compilati
 *   bin/cake regenerate_ai_content --type=seo --model=Percorsi --force-override  # sovrascrive TUTTI i Percorsi pubblicati
 *   bin/cake regenerate_ai_content --type=description --override-if-less=500 # vuote o con meno di 500 caratteri di testo
 *   bin/cake regenerate_ai_content --type=seo --dry-run                    # mostra solo l'elenco
 *   bin/cake regenerate_ai_content --type=seo --ai-model=claude --sleep=5
 */
class RegenerateAiContentCommand extends Command
{
    /**
     * Mappa risorsa -> tabella/service/campi. 'contain' è l'unione di ciò che serve sia al
     * flusso SEO sia a quello descrizione, per non dover ramificare la query in base a --type.
     * Usata anche da TranslateContentCommand.
     */
    public const SEO_FIELDS = ['seo_description', 'seo_keywords'];

    public const TARGETS = [
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
            ->addOption('force-override', [
                'boolean' => true,
                'default' => false,
                'help' => 'Ignora il filtro "campo mancante": rigenera e sovrascrive anche le risorse già compilate (rispetta comunque --model/--destination, e "published").',
            ])
            ->addOption('override-if-less', [
                'help' => 'Seleziona anche le risorse il cui testo (senza HTML) è più corto di N caratteri, oltre a quelle vuote.',
            ])
            ->addOption('skip-translate', [
                'boolean' => true,
                'default' => false,
                'help' => 'Non tradurre in inglese il contenuto generato.',
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

        // In CLI I18n parte da App.defaultLocale (it_IT), che il Translate behavior (defaultLocale
        // 'ita') tratterebbe come una traduzione: leggiamo/salviamo sulla tabella principale.
        I18n::setLocale('ita');
        $translate = !$args->getOption('skip-translate');
        $translator = new AutoTranslateService();

        $type = (string)$args->getOption('type');
        $modelOption = $args->getOption('model');
        $idOption = $args->getOption('id');
        $destinationOption = $args->getOption('destination');
        $aiModel = $args->getOption('ai-model') ?: null;
        $sleepSeconds = max(0, (int)$args->getOption('sleep'));
        $dryRun = (bool)$args->getOption('dry-run');
        $skipConfirm = (bool)$args->getOption('yes');
        $forceOverride = (bool)$args->getOption('force-override');
        $minLength = (int)$args->getOption('override-if-less');
        if ($args->getOption('override-if-less') !== null && $minLength <= 0) {
            $io->error('--override-if-less richiede un numero di caratteri maggiore di 0.');
            return static::CODE_ERROR;
        }

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

            $fieldToCheck = $type === 'seo' ? 'seo_description' : $config['descriptionField'];
            if ($ids !== null) {
                $query->where(["$alias.id IN" => $ids]);
            } elseif ($forceOverride || $minLength > 0) {
                // con --override-if-less il filtro sulla lunghezza è in PHP (serve il testo senza HTML)
                $query->where(["$alias.published" => 1]);
            } else {
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
                if ($ids === null && !$forceOverride && $minLength > 0 && self::textLength($entity->$fieldToCheck) >= $minLength) {
                    continue;
                }
                $jobs[] = ['target' => $targetName, 'entity' => $entity, 'config' => $config];
            }
        }

        $total = count($jobs);
        $io->out("Tipo contenuto: <info>{$type}</info>");
        $io->out('Risorse: <info>' . implode(', ', $targetNames) . '</info>');
        if ($ids === null && $forceOverride) {
            $io->warning('Modalità: --force-override attivo, verranno sovrascritte anche le risorse già compilate.');
        } elseif ($ids === null && $minLength > 0) {
            $io->out("Filtro: vuote o con meno di <info>{$minLength}</info> caratteri di testo");
        }
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
        $startTime = microtime(true);
        $startGenerationId = $this->currentMaxGenerationId();

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
                        if ($translate) {
                            $this->translate($translator, $table, $entity, self::SEO_FIELDS, $io);
                        }
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
                        if ($translate) {
                            $this->translate($translator, $table, $entity, [$descField], $io);
                        }
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
        $io->out('Tempo impiegato: <info>' . $this->formatDuration(microtime(true) - $startTime) . '</info>');

        $credits = $this->creditsSpentSince($startGenerationId);
        if ($credits === null) {
            $io->out('Crediti spesi: <info>n/d</info> (tabella ai_generations non disponibile su questo sito)');
        } else {
            $io->out(sprintf(
                'Crediti spesi: <info>%d</info> generazioni, <info>%d</info> token totali, <info>$%s</info>',
                $credits['count'],
                $credits['total_tokens'],
                number_format($credits['total_cost'], 6)
            ));
        }

        return $failed === 0 ? static::CODE_SUCCESS : static::CODE_ERROR;
    }

    /**
     * Traduzione in inglese del contenuto appena generato: un errore qui non conta come
     * generazione fallita (l'italiano è già salvato), viene solo segnalato.
     */
    private function translate(AutoTranslateService $translator, $table, $entity, array $fields, ConsoleIo $io): void
    {
        try {
            $translator->toEnglish($table, $entity, $fields);
            $io->out('  tradotto in inglese: ' . implode(', ', $fields));
        } catch (\Throwable $e) {
            $io->warning('  Traduzione in inglese fallita: ' . $e->getMessage());
        }
    }

    /**
     * Id massimo attuale in AiGenerations, usato come "spartiacque" per isolare solo le righe
     * inserite da questo run (vedi creditsSpentSince()). Null se la tabella non è disponibile
     * su questo sito (non ancora migrata) — in quel caso il resoconto crediti viene omesso.
     */
    private function currentMaxGenerationId(): ?int
    {
        try {
            $table = TableRegistry::getTableLocator()->get('AiGenerations');
            $query = $table->find();
            $row = $query->select(['maxId' => $query->func()->max('id')])->first();

            return (int)($row->maxId ?? 0);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Somma token/costo/numero di generazioni registrate in AiGenerations con id > $sinceId,
     * cioè quelle prodotte da questo run (loggate automaticamente da AiRouterService::callAiRaw()
     * per ogni chiamata OpenRouter, anche quando la generazione fallisce nel parsing a valle:
     * il costo è comunque stato addebitato da OpenRouter). Null se non disponibile.
     */
    private function creditsSpentSince(?int $sinceId): ?array
    {
        if ($sinceId === null) {
            return null;
        }

        try {
            $table = TableRegistry::getTableLocator()->get('AiGenerations');
            $query = $table->find();
            $row = $query
                ->select([
                    'count' => $query->func()->count('id'),
                    'total_tokens' => $query->func()->sum('total_tokens'),
                    'total_cost' => $query->func()->sum('cost'),
                ])
                ->where(['id >' => $sinceId])
                ->first();

            return [
                'count' => (int)($row->count ?? 0),
                'total_tokens' => (int)($row->total_tokens ?? 0),
                'total_cost' => (float)($row->total_cost ?? 0),
            ];
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Lunghezza del testo visibile: senza tag HTML ed entità, spazi compattati.
     */
    public static function textLength(?string $value): int
    {
        $text = html_entity_decode(strip_tags((string)$value), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return mb_strlen(trim((string)preg_replace('/\s+/u', ' ', $text)));
    }

    private function formatDuration(float $seconds): string
    {
        if ($seconds < 60) {
            return sprintf('%.1fs', $seconds);
        }

        $minutes = (int)floor($seconds / 60);
        $rest = (int)round($seconds - $minutes * 60);

        return sprintf('%dm %02ds', $minutes, $rest);
    }
}
