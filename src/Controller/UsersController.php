<?php

declare(strict_types=1);

namespace App\Controller;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
{

  public function beforeFilter(\Cake\Event\EventInterface $event)
  {
    parent::beforeFilter($event);
    $this->loadComponent('Authentication.Authentication');
    $this->Authentication->allowUnauthenticated(['login','add']);
  }

  public function login()
  {
    $result = $this->Authentication->getResult();
    // If the user is logged in send them away.
    if ($result->isValid()) {
      $target = $this->Authentication->getLoginRedirect() ?? '/admin';
      return $this->redirect($target);
    }
    if ($this->request->is('post') && !$result->isValid()) {
      $this->Flash->error('Invalid username or password');
    }
  }

  public function logout()
  {
    $this->Authentication->logout();
    return $this->redirect(['controller' => 'Users', 'action' => 'login']);
  }


  public function add()
  {
    $user = $this->Users->newEmptyEntity();
    if ($this->request->is('post')) {
      $user = $this->Users->patchEntity($user, $this->request->getData());
      if ($this->Users->save($user)) {
        $this->Flash->success(__('The user has been saved.'));
        return $this->redirect(['action' => 'add']);
      }
      $this->Flash->error(__('Unable to add the user.'));
    }
    $this->set('user', $user);
  }
}
