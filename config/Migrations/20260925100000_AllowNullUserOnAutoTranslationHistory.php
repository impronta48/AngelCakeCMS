<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

/**
 * Le traduzioni lanciate da CLI (regenerate_ai_content, translate_content) non hanno un
 * utente: user_id NULL = traduzione di sistema.
 */
class AllowNullUserOnAutoTranslationHistory extends AbstractMigration
{
    public function up(): void
    {
        $this->table('auto_translation_history')
            ->changeColumn('user_id', 'integer', ['null' => true, 'default' => null, 'signed' => true])
            ->update();
    }

    public function down(): void
    {
        $this->table('auto_translation_history')
            ->changeColumn('user_id', 'integer', ['null' => false, 'signed' => true])
            ->update();
    }
}
