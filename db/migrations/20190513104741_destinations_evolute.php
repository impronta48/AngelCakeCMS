<?php

use Phinx\Migration\AbstractMigration;

class DestinationsEvolute extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Any other destructive changes will result in an error when trying to
     * rollback the migration.
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        // create the table
        $table = $this->table('destinations');
        if (!$table->hasColumn('show_in_list')){
            $table->addColumn('show_in_list', 'boolean', ['default' =>TRUE])
              ->addColumn('facebook', 'string', ['limit' => 255])
              ->addColumn('instagram', 'string', ['limit' => 255])
              ->addColumn('created', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
              ->addColumn('modified', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])              
              ->save();
        }
    }
}
