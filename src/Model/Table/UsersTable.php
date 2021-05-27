<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\Datasource\EntityInterface;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Session;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Users Model
 *
 * @property \App\Model\Table\ArticlesTable&\Cake\ORM\Association\HasMany $Articles
 * @property \App\Model\Table\EventsTable&\Cake\ORM\Association\HasMany $Events
 * @property \App\Model\Table\SocialAccountsTable&\Cake\ORM\Association\HasMany $SocialAccounts
 *
 * @method \App\Model\Entity\User newEmptyEntity()
 * @method \App\Model\Entity\User newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\User[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\User get($primaryKey, $options = [])
 * @method \App\Model\Entity\User findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\User patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\User[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\User|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UsersTable extends Table
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

    $this->setTable('users');
    $this->setDisplayField('gmail');
    $this->setPrimaryKey('id');

    $this->addBehavior('Timestamp');

    $this->hasMany('Articles', [
      'foreignKey' => 'user_id',
    ]);
    $this->hasMany('Events', [
      'foreignKey' => 'user_id',
    ]);
    $this->hasMany('SocialAccounts', [
      'foreignKey' => 'user_id',
    ]);
    $this->belongsTo('Destinations');
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
      ->allowEmptyString('id', null, 'create');

    $validator
      ->scalar('username')
      ->maxLength('username', 255)
      ->requirePresence('username', 'create')
      ->notEmptyString('username');

    $validator
      ->email('email')
      ->allowEmptyString('email');

    $validator
      ->scalar('password')
      ->maxLength('password', 255)
      ->requirePresence('password', 'create')
      ->notEmptyString('password');


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
    $rules->add($rules->isUnique(['username']), ['errorField' => 'username']);
    $rules->add($rules->isUnique(['email']), ['errorField' => 'email']);
    $rules->add($rules->isUnique(['gmail']), ['errorField' => 'gmail']);
    $rules->add($rules->isUnique(['fbmail']), ['errorField' => 'fbmail']);

    return $rules;
  }

  public function getUser(EntityInterface $profile, Session $session)
  {
    // Make sure here that all the required fields are actually present
    if (empty($profile->email)) {
      throw new \RuntimeException('Could not find email in social profile.');
    }

    /*     // If you want to associate the social entity with currently logged in user
    // use the $session argument to get user id and find matching user entity.
    $userId = $session->read('Auth.User.id');
    if ($userId) {
      return $this->get($userId);
    } */

    // Check if user with same email exists. This avoids creating multiple
    // user accounts for different social identities of same user. You should
    // probably skip this check if your system doesn't enforce unique email
    // per user.
    $user = $this->find()
      ->where(['gmail' => $profile->email])
      ->first();

    if ($user) {
      return $user;
    } else {
      throw new NotFoundException("Impossibile trovare l'utente collegato");
    }

    // Create new user account
    //$user = $this->newEntity(['email' => $profile->email]);
    //$user = $this->save($user);

    //if (!$user) {
    //  throw new \RuntimeException('Unable to save new user');
    //}

    //return $user;
  }
}
