<?php

declare(strict_types=1);

namespace App\Service;

use AiRouter\Service\AiRouterService;
use Cake\Core\Configure;
use Cake\Log\Log;

/**
 * Motore comune per generare seo_description/seo_keywords via AI (OpenRouter).
 *
 * Le sottoclassi forniscono solo: la chiave della sezione specifica in
 * config/airouter.php (Cyclomap.airouter, Configure key "AiRouter") e il
 * contesto (array) da passare al modello. Il merge global + specifica +
 * vincolo di formato, la chiamata e il parsing sono qui, in un posto solo.
 */
abstract class AbstractSeoAiService
{
    /**
     * Vincolo tecnico di formato (non una best practice SEO): serve a garantire
     * che parseJson() riesca sempre a estrarre seo_description/seo_keywords.
     */
    private const OUTPUT_FORMAT = <<<PROMPT
# OUTPUT FORMAT (vincolante, sostituisce qualunque indicazione di formato data sopra)

Rispondi SOLO con un JSON valido, senza testo introduttivo né blocchi di codice, con questa forma esatta:
{"seo_description": "...", "seo_keywords": "..."}

"seo_keywords": 5-8 parole chiave separate da virgola, specifiche; evita parole generiche isolate come "bici" da sola.
PROMPT;

    public function __construct(protected readonly AiRouterService $aiRouter = new AiRouterService())
    {
    }

    /**
     * Chiave sotto AiRouter.system_prompt.seo.* in config/airouter.php (es. 'poi', 'noleggiatore', 'destinazione').
     */
    abstract protected function promptSection(mixed $entity): string;

    /**
     * Dati dell'entità da passare al modello come contesto (solo campi noti/veri, mai inventati).
     */
    abstract protected function buildContext(mixed $entity): array;

    /**
     * @return array{seo_description: string, seo_keywords: string}|null
     */
    public function generate(mixed $entity, ?string $model = null): ?array
    {
        $model ??= (string)(Configure::read('AiRouter.default_model') ?? 'gemini');

        $systemPrompt = trim(implode("\n\n", array_filter([
            (string)Configure::read('AiRouter.system_prompt.global'),
            (string)Configure::read('AiRouter.system_prompt.seo.' . $this->promptSection($entity)),
            self::OUTPUT_FORMAT,
        ])));

        $userMessage = "Genera la seo_description e le seo_keywords per questa pagina:\n"
            . json_encode($this->buildContext($entity), JSON_UNESCAPED_UNICODE);

        Log::write('debug', "Prompt: $userMessage with $systemPrompt");

        $raw = $this->aiRouter->callAiRaw($model, $systemPrompt, $userMessage, resourceContext: [
            'fk_model' => $entity->getSource() ?: null,
            'fk_id'    => $entity->id ?? null,
        ]);
        if ($raw === null) {
            return null;
        }

        $data = $this->aiRouter->parseJson($raw);
        if (empty($data['seo_description'])) {
            return null;
        }

        return [
            'seo_description' => (string)$data['seo_description'],
            'seo_keywords'    => (string)($data['seo_keywords'] ?? ''),
        ];
    }
}
