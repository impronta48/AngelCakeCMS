<?php

declare(strict_types=1);

namespace App\Model\Behavior;

use Tags\Model\Behavior\TagBehavior as BaseTagBehavior;

class TagBehavior extends BaseTagBehavior
{
    public function bindAssociations(): void
    {
        parent::bindAssociations();

        $tagsAlias = $this->getConfig('tagsAlias');
        $taggedAlias = $this->getConfig('taggedAlias');
        $taggedTable = $this->_table->{$taggedAlias}->getTarget();

        // Sovrascrivi Tags su Tagged prima
        if ($taggedTable->hasAssociation($tagsAlias)) {
            $taggedTable->associations()->remove($tagsAlias);
            $taggedTable->belongsTo($tagsAlias, [
                'className' => \App\Model\Table\TagsTable::class,
                'foreignKey' => $this->getConfig('tagsAssoc.targetForeignKey'),
                'joinType' => 'INNER',
            ]);
        }

        // Poi sovrascrivi su Destinations (o qualunque table usa il behavior)
        $this->_table->associations()->remove($tagsAlias);
        $this->_table->belongsToMany($tagsAlias, array_merge(
            $this->getConfig('tagsAssoc'),
            [
                'className' => \App\Model\Table\TagsTable::class,
                'through' => $taggedTable,
                'conditions' => [
                    $taggedAlias . '.' . $this->getConfig('fkModelField') =>
                        $this->getConfig('fkModelAlias') ?: $this->_table->getAlias(),
                ],
            ]
        ));
    }
}
