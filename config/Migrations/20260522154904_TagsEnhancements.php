<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class TagsEnhancements extends AbstractMigration
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
        $table = $this->table('tags_enhancements');
        $table->addColumn('tag_id', 'integer', ['null' => false])
            ->addColumn('alt_name', 'string', ['null' => true])
            ->addColumn('color', 'string', ['limit' => 7, 'null' => true])
            ->addColumn('created', 'datetime', ['null' => true])
            ->addColumn('modified', 'datetime', ['null' => true])
            ->addForeignKey('tag_id', 'tags_tags', 'id', ['delete' => 'CASCADE'])
            ->addIndex(['tag_id'], ['unique' => true])
            ->create();
    }
}
