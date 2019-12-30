<?php

use Phinx\Migration\AbstractMigration;

class UtentiArticoli extends AbstractMigration
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
        $table = $this->table('articles');
        if ($table->hasColumn('user_id')){
            $table->renameColumn('user_id', 'char', ['limit' =>36])
                ->save();
        }
        else {
            $table->addColumn('user_id', 'char', ['limit' =>36])
                ->save();
        }
        $builder = $this->getQueryBuilder();
        $builder
            ->update('articles')
            ->set('user_id', '7aab5817-8f9f-4a34-91b2-3c22a0c6e3d7')
            ->execute();
    }
}
