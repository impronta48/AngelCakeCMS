<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class AddParentDestination extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * @return void
     */
    public function change(): void
    {
        $table = $this->table('destinations');

        $table->addColumn('parent_id', 'integer', [
            'default' => null,
            'null'    => true,
            'signed'  => true,
            
            'comment' => 'Self-referential foreign key to destinations.id',
        ]);

        $table->addIndex(['parent_id'], [
            'name' => 'idx_destinations_parent_id',
        ]);

        $table->addForeignKey('parent_id', 'destinations', 'id', [
            'delete'     => 'SET_NULL',
            'update'     => 'CASCADE',
            'constraint' => 'fk_destinations_parent_id',
        ]);

        $table->update();
    }

    /**
     * Rollback: remove the column and its constraints.
     *
     * @return void
     */
    public function down(): void
    {
        $table = $this->table('destinations');

        $table->dropForeignKey('parent_id', 'fk_destinations_parent_id');
        $table->removeIndex(['parent_id']);
        $table->removeColumn('parent_id');

        $table->update();
    }
}