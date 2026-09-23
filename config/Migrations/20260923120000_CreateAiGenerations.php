<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateAiGenerations extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('ai_generations');
        $table->addColumn('generation_id', 'string', ['limit' => 100, 'null' => true])   // "id" di OpenRouter, es. "gen-1790165174-..."
              ->addColumn('fk_model', 'string', ['limit' => 255, 'null' => false])        // 'Poi' | 'Percorsi' | 'Destinations' — stessa convenzione di tags_tagged.fk_model
              ->addColumn('fk_id', 'integer', ['limit' => 11, 'null' => false])           // stessa convenzione di tags_tagged.fk_id
              ->addColumn('ai_model', 'string', ['limit' => 255, 'null' => true])         // $body['model'], es. "google/gemini-2.5-flash"
              ->addColumn('object', 'string', ['limit' => 32, 'null' => true])            // "chat.completion"
              ->addColumn('created', 'integer', ['null' => true])                         // timestamp unix di OpenRouter, salvato grezzo
              ->addColumn('provider', 'string', ['limit' => 64, 'null' => true])
              ->addColumn('finish_reason', 'string', ['limit' => 32, 'null' => true])
              ->addColumn('total_tokens', 'integer', ['null' => true])
              ->addColumn('cost', 'decimal', ['precision' => 12, 'scale' => 6, 'null' => true]) // dollari — decimal, non float, per evitare drift sulle somme
              ->addIndex(['fk_model', 'fk_id'])
              ->create();
    }
}
