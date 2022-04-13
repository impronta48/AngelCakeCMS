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
    $this->Authentication->allowUnauthenticated(['login','old']);
  }

  public function login()
  {
    $result = $this->Authentication->getResult();
    // If the user is logged in send them away.
    if ($result->isValid()) {
      $target = $this->Authentication->getLoginRedirect() ?? '/home';
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
}
