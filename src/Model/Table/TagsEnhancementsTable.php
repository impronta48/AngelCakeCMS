<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * TagsEnhancements Model
 *
 * @property \App\Model\Table\TagsTable&\Cake\ORM\Association\BelongsTo $Tags
 *
 * @method \App\Model\Entity\TagsEnhancement newEmptyEntity()
 * @method \App\Model\Entity\TagsEnhancement newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\TagsEnhancement[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\TagsEnhancement get($primaryKey, $options = [])
 * @method \App\Model\Entity\TagsEnhancement findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\TagsEnhancement patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\TagsEnhancement[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\TagsEnhancement|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\TagsEnhancement saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\TagsEnhancement[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\TagsEnhancement[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\TagsEnhancement[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\TagsEnhancement[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class TagsEnhancementsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('tags_enhancements');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Tags', [
            'foreignKey' => 'tag_id',
            'joinType' => 'INNER',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('tag_id')
            ->notEmptyString('tag_id')
            ->add('tag_id', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('alt_name')
            ->maxLength('alt_name', 255)
            ->allowEmptyString('alt_name');

        $validator
            ->scalar('color')
            ->maxLength('color', 7)
            ->allowEmptyString('color');

     
        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn('tag_id', 'Tags'), ['errorField' => 'tag_id']);

        return $rules;
    }
}
