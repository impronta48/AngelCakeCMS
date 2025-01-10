<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Exception;
use Cake\Core\Configure;
use Cake\Core\Plugin;

/**
 * Events Model
 *
 * @property \App\Model\Table\DestinationsTable|\Cake\ORM\Association\BelongsTo $Destinations
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\Event get($primaryKey, $options = [])
 * @method \App\Model\Entity\Event newEmptyEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Event[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Event|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Event|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Event patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Event[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Event findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class EventsTable extends Table
{

	/**
	 * Initialize method
	 *
	 * @param array $config The configuration for the Table.
	 * @return void
	 */
	public function initialize(array $config): void {
		parent::initialize($config);

		$this->setTable('events');
		$this->setDisplayField('title');
		$this->setPrimaryKey('id');

		$this->addBehavior('Timestamp');

		$this->belongsTo('Destinations', [
			'foreignKey' => 'destination_id',
		]);
		$this->belongsTo('Users', [
			'foreignKey' => 'user_id',
		]);


		//Devo commentare questa relazione perchè non è detto che cyclomap sia presente
		if (Plugin::isLoaded('Cyclomap')) {
			$this->belongsTo('Cyclomap.Percorsi', [
				'foreignKey' => 'percorso_id',
			]);
		}

		$this->hasMany('Participants');
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
			->scalar('title')
			->maxLength('title', 255)
			->allowEmpty('title');

		$validator
			->scalar('description')
			->allowEmpty('description');

		$validator
			->integer('max_pax')
			->allowEmpty('max_pax');

		$validator
			->scalar('place')
			->maxLength('place', 255)
			->allowEmpty('place');

		$validator
			->dateTime('start_time')
			->allowEmpty('start_time');

		$validator
			->dateTime('end_time')
			->allowEmpty('end_time');

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
		$rules->add($rules->existsIn(['destination_id'], 'Destinations'));
		$rules->add($rules->existsIn(['percorso_id'], 'Percorsi'));
		$rules->add($rules->existsIn(['user_id'], 'Users'));

		return $rules;
	}

	public function beforeSave(\Cake\Event\EventInterface $event, $entity, $options) {
		if($entity->percorso_id) {
			// forza i campi collegati al percorso scelto
			$percorso = $this->Percorsi->findById($entity->percorso_id)->firstOrFail();
			if(empty($percorso) || $percorso->tipo_id != Configure::read('TipiPercorsi.evento')) {
				throw new Exception('Invalid percorso evento');
			}
			$entity->title = $percorso->title;
			$entity->description = $percorso->descr;
			$entity->destination_id = $percorso->destination_id;
			$entity->place = $percorso->comune;
			$entity->cost = $percorso->a_partire_da_prezzo;
		}	
	}
}
