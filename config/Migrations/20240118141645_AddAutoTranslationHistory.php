<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddAutoTranslationHistory extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('auto_translation_history');
        $table->addColumn('context', 'string', [
            'default' => null,
            'limit' => 1024,
            'null' => false,
        ]);
        $table->addColumn('source', 'text', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('translation', 'text', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('user_id', 'integer', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('has_invoice', 'boolean', [
            'default' => false,
            'null' => false,
        ]);
        $table->addColumn('paid', 'boolean', [
            'default' => false,
            'null' => false,
        ]);
        $table->addColumn('created', 'datetime', [
            'default' => 'CURRENT_TIMESTAMP',
            'null' => false,
        ]);
        $table->addColumn('modified', 'datetime', [
            'default' => 'CURRENT_TIMESTAMP',
            'null' => false,
        ]);
        $table->create();
    }
}
