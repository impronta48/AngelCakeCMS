<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\Event\EventInterface;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Utility\Text;
use ArrayObject;

/**
 * Tags Model
 *
 * @property \App\Model\Table\ArticlesTable|\Cake\ORM\Association\BelongsToMany $Articles
 *
 * @method \App\Model\Entity\Tag get($primaryKey, $options = [])
 * @method \App\Model\Entity\Tag newEmptyEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Tag[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Tag|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Tag|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Tag patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Tag[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Tag findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class TagsTable extends Table
{
	/**
	 * Initialize method
	 *
	 * @param array $config The configuration for the Table.
	 * @return void
	 */
	public function initialize(array $config): void {
		parent::initialize($config);

		$this->setTable('tags_tags');
		$this->setDisplayField('label');
		$this->setPrimaryKey('id');

		$this->addBehavior('Timestamp');

		$this->belongsTo('Destinations', [
			'className' => 'Destinations',
			'foreignKey' => 'namespace',
			'bindingKey' => 'id',
			'joinType' => 'LEFT',
		]);

		$this->hasOne('TagsEnhancements', [
            'foreignKey' => 'tag_id',
            'className'  => 'TagsEnhancements',
        ]);

	}


	

	/**
	 * Default validation rules.
	 *
	 * @param \Cake\Validation\Validator $validator Validator instance.
	 * @return \Cake\Validation\Validator
	 */
	public function validationDefault(Validator $validator): \Cake\Validation\Validator {
		$validator
			->integer('id')
			->allowEmpty('id', 'create');

		$validator
			->scalar('label')
			->maxLength('label', 255)
			->requirePresence('label', 'create')
			->notEmptyString('label')
			->add('label', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

		return $validator;
	}

	/**
	 * Returns a rules checker object that will be used for validating
	 * application integrity.
	 *
	 * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
	 * @return \Cake\ORM\RulesChecker
	 */
	public function buildRules(RulesChecker $rules): \Cake\ORM\RulesChecker {
		$rules->add($rules->isUnique(['label']));

		return $rules;
	}

	public function beforeSave(EventInterface $event, EntityInterface $entity, ArrayObject $options): void
	{
		if ($entity->isNew() || $entity->isDirty('label')) {
			$slug = Text::slug((string)$entity->get('label'));
			$entity->set('slug', strtolower($slug));
		}
	}



	 // Enhancement automatico su ogni find()
    public function beforeFind(EventInterface $event, SelectQuery $query, ArrayObject $options): void
    {
        $contain = $query->getContain();
        if (!isset($contain['TagsEnhancements'])) {
            $query->contain(['TagsEnhancements']);
        }
    }
}
