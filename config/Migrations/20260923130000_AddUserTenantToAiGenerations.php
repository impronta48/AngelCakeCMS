<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddUserTenantToAiGenerations extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('ai_generations');
        $table->addColumn('user', 'string', ['limit' => 255, 'null' => false, 'default' => 'system'])   // username di chi ha richiesto la generazione, o 'system' (CLI/batch)
              ->addColumn('tenant', 'string', ['limit' => 255, 'null' => false, 'default' => 'default']) // sito/tenant di provenienza, da Configure::read('confPath')
              ->update();
    }
}
