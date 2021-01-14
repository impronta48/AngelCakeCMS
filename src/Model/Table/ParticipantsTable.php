<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Participants Model
 *
 * @property \App\Model\Table\EventsTable|\Cake\ORM\Association\BelongsTo $Events
 *
 * @method \App\Model\Entity\Participant get($primaryKey, $options = [])
 * @method \App\Model\Entity\Participant newEmptyEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Participant[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Participant|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Participant|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Participant patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Participant[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Participant findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ParticipantsTable extends Table
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

        $this->setTable('participants');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Events', [
            'foreignKey' => 'event_id',
            'joinType' => 'INNER'
        ]);
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
            ->maxLength('name', 255)
            ->requirePresence('name', 'create')
            ->notEmpty('name');

        $validator
            ->scalar('surname')
            ->maxLength('surname', 255)
            ->requirePresence('surname', 'create')
            ->notEmpty('surname');

        $validator
            ->email('email')
            ->notEmpty('email');

        $validator
            ->scalar('tel')
            ->maxLength('tel', 50)
            ->notEmpty('tel');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): \Cake\ORM\RulesChecker
    {
        $rules->add($rules->existsIn(['event_id'], 'Events'));

        //Questa regola sarebbe quella giusta ma non riesco a farla funzionare
/*        $rules->add(
            function ($entity, $options) use ($rules) {
                $query = $this->Events->find();
                $max_pax = $query->select('max_pax')
                    ->where(['id'=>$entity->event_id])
                    ->first();
                $r =  $rules->validCount('events', $max_pax , '<=', "Questo evento prevede al massimo $max_pax partecipanti");
                debug($r);
                return $r;
            },
            'maxPax',
            [
                'errorField' => 'event_id',
                'message' => "L'evento è al completo ti chiediamo di scegliere un altro corso"
            ]
        );*/

        return $rules;
    }

    //Controllo se un partecipante è di troppo in un evento
    public function checkMaxPax($participant)
    {
            //Dobbiamo controllare se abbiamo raggiunto il numero massimo di partecipanti
            //Controllo l'evento richiesto
            $e = $participant->event_id;

            //Verifico il numero massimo di pax per questo evento
            $query = $this->Events->find();            
            $max_pax = $query->select(['max_pax'])
                ->where(['id'=>$e])
                ->first();

             if (!empty($max_pax)){
                $max_pax= $max_pax->max_pax;	
             }
             

            //Se non è specificato considero che non ci sia limite
            if (empty($max_pax) || $max_pax == 0  )
            {
                return false;
            }


            //Conto quanti partecipanti ha quell'evento
            // Results in SELECT COUNT(*) count FROM ...
            $query = $this->find();
            $c= $query->select(['count' => $query->func()->count('*')])
                ->where(['event_id'=>$e])
                ->first()
                ->count;        //questo è il nome del campo

            return ($c >= $max_pax);
    }
}
