<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\Cache\Cache;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Destinations Model
 *
 * @property \App\Model\Table\EventsTable|\Cake\ORM\Association\HasMany $Events
 *
 * @method \App\Model\Entity\Destination get($primaryKey, $options = [])
 * @method \App\Model\Entity\Destination newEmptyEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Destination[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Destination|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Destination|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Destination patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Destination[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Destination findOrCreate($search, callable $callback = null, $options = [])
 */
class DestinationsTable extends Table
{
	/**
	 * Initialize method
	 *
	 * @param array $config The configuration for the Table.
	 * @return void
	 */
	public function initialize(array $config): void
	{
		$this->addBehavior('Translate', [
			'fields' => [
				'nomiseo',
				'title',
				'name',
				'descrizione',
				'preposition'
			],
			'referenceName' => 'Destination', //Importante per garantire la compatibilità con cake2
			'defaultLocale' => 'ita',
			// 'allowEmptyTranslations' => false,
		]);

		parent::initialize($config);

		$this->setTable('destinations');
		$this->setDisplayField('name');
		$this->setPrimaryKey('id');

		$this->hasMany('Events', [
			'foreignKey' => 'destination_id',
		]);
		$this->hasMany('Articles');
	}

	public function beforeSave(\Cake\Event\EventInterface $event, $entity, $options)
	{
		Cache::clear('_cake_routes_');
	}

	/**
	 * Default validation rules.
	 *
	 * @param \Cake\Validation\Validator $validator Validator instance.
	 * @return \Cake\Validation\Validator
	 */
	public function validationDefault(Validator $validator): \Cake\Validation\Validator
	{
		$validator
			->integer('id')
			->allowEmpty('id', 'create');

		$validator
			->scalar('name')
			->maxLength('name', 250)
			->allowEmpty('name');

		$validator
			->scalar('slug')
			->maxLength('slug', 250)
			->allowEmpty('slug');

		return $validator;
	}
}
