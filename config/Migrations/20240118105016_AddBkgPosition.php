<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

// introduce dati arbitrari come key-value pairs
class AddBkgPosition extends AbstractMigration
{
    public function change() {
        $table = $this->table('percorsi');
        $table->addColumn('copertina_bkg_pos', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => true,
        ]);
        $table->update();

        $table = $this->table('poi');
        $table->addColumn('copertina_bkg_pos', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => true,
        ]);
        $table->update();

        $table = $this->table('articles');
        $table->addColumn('copertina_bkg_pos', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => true,
        ]);
        $table->update();

        $table = $this->table('destinations');
        $table->addColumn('copertina_bkg_pos', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => true,
        ]);
        $table->update();
    }
}
