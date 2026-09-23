<?php

declare(strict_types=1);

namespace App\Service;

use AiRouter\Service\AiRouterService;
use Cake\Core\Configure;
use Cake\Log\Log;

/**
 * Motore comune per riscrivere/generare il corpo (campo `descr`) di poi e
 * percorsi tramite AI (OpenRouter). Stesso schema di AbstractSeoAiService,
 * ma legge/scrive sotto la chiave Configure "AiRouter.system_prompt.editoriale"
 * e produce testo editoriale invece di una meta description.
 */
abstract class AbstractDescriptionAiService
{
    protected const OUTPUT_FORMAT_HTML = <<<PROMPT
# OUTPUT FORMAT (vincolante, sostituisce qualunque indicazione di formato data sopra)

Rispondi SOLO con un JSON valido, senza testo introduttivo né blocchi di codice, con questa forma esatta:
{"descr": "<p>...</p>"}

Il contenuto di "descr" è HTML semplice (solo <p> e, se utile, <h3> per eventuali sottotitoli), pronto per un editor WYSIWYG. Nessun markdown, nessun tag di stile o script.
PROMPT;

    protected const OUTPUT_FORMAT_PLAIN = <<<PROMPT
# OUTPUT FORMAT (vincolante, sostituisce qualunque indicazione di formato data sopra)

Rispondi SOLO con un JSON valido, senza testo introduttivo né blocchi di codice, con questa forma esatta:
{"descr": "..."}

Il contenuto di "descr" è testo semplice, senza HTML né markdown; eventuali paragrafi separati da una riga vuota.
PROMPT;

    public function __construct(protected readonly AiRouterService $aiRouter = new AiRouterService())
    {
    }

    /**
     * Chiave sotto AiRouter.system_prompt.editoriale.* in config/airouter.php (es. 'poi', 'percorso').
     */
    abstract protected function promptSection(mixed $entity): string;

    /**
     * Dati dell'entità da passare al modello come contesto (solo campi noti/veri, mai inventati).
     */
    abstract protected function buildContext(mixed $entity): array;

    /**
     * Se false, il prompt della sezione specifica (promptSection()) viene inviato da solo,
     * senza il preambolo condiviso "editoriale.global" — utile quando quella sezione è già
     * un prompt completo e autonomo (es. destinazione).
     */
    protected function includeGlobalPrompt(): bool
    {
        return true;
    }

    /**
     * Forma del campo `descr` prodotto: HTML per un editor WYSIWYG (default, poi/percorso)
     * o testo semplice (es. destinazione, il cui campo è un textarea).
     */
    protected function outputFormat(): string
    {
        return self::OUTPUT_FORMAT_HTML;
    }

    /**
     * @param string|null $user Username di chi ha richiesto la generazione (bottone admin);
     *   null (default, es. da un comando batch) viene loggato come 'system' su AiGenerations.
     */
    public function generate(mixed $entity, ?string $model = null, ?string $user = null): ?string
    {
        $model ??= (string)(Configure::read('AiRouter.default_model') ?? 'gemini');

        $systemPrompt = trim(implode("\n\n", array_filter([
            $this->includeGlobalPrompt() ? (string)Configure::read('AiRouter.system_prompt.editoriale.global') : '',
            (string)Configure::read('AiRouter.system_prompt.editoriale.' . $this->promptSection($entity)),
            $this->outputFormat(),
        ])));

        $userMessage = "Riscrivi/genera la descrizione editoriale per questa pagina:\n"
            . json_encode($this->buildContext($entity), JSON_UNESCAPED_UNICODE);

        Log::write('debug', "Prompt: $userMessage with $systemPrompt");

        $raw = $this->aiRouter->callAiRaw($model, $systemPrompt, $userMessage, resourceContext: [
            'fk_model' => $entity->getSource() ?: null,
            'fk_id'    => $entity->id ?? null,
            'user'     => $user ?: 'system',
        ]);
        if ($raw === null) {
            return null;
        }

        $data = $this->aiRouter->parseJson($raw);
        if (empty($data['descr'])) {
            return null;
        }

        return (string)$data['descr'];
    }
}
