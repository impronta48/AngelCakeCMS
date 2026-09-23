<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddSeoToDestinations extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change(): void
    {
        $table = $this->table('destinations');
        $table->addColumn('seo_description', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => true,
        ]);
        $table->addColumn('seo_keywords', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => true,
        ]);
        $table->update();
    }
}
