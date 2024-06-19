<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;

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
    $this->Authentication->allowUnauthenticated(['login']);
    // $this->Authentication->authorizeModel('add', 'edit', 'delete');
  }

  /**
   * Index method
   *
   * @return \Cake\Http\Response|null|void Renders view
   */
  public function index()
  {
    $roles = ADMIN_ROLES_LIST ?? [];
    $query = $this->Users->find()
              ->contain(['Destinations']);

    if (count($roles)) {
      $query->where(['group_id IN' => $roles]);
    }            

    $this->Authorization->applyScope($query);

    $users = $this->paginate($query);

    $this->set(compact('users'));
  }

  /**
   * View method
   *
   * @param string|null $id User id.
   * @return \Cake\Http\Response|null|void Renders view
   * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
   */
  public function view($id = null)
  {
    $user = $this->Users->get($id, [
      'contain' => ['Articles', 'Events', 'SocialAccounts'],
    ]);

    $this->Authorization->authorize($user);

    $this->set(compact('user'));
  }

  /**
   * Add method
   *
   * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
   */
  public function add()
  {
    $user = $this->Users->newEmptyEntity();
    if ($this->request->is('post')) {

      $user = $this->Users->patchEntity($user, $this->request->getData());
      $user->username = $user->gmail;
      $user->password = 'IMPOSSIBILE' . rand(0, 12345);      
      $this->Authorization->authorize($user);

      if ($this->Users->save($user)) {
        $this->Flash->success(__('The user has been saved.'));

        return $this->redirect(['action' => 'index']);
      }
      $this->Flash->error(__('The user could not be saved. Please, try again.'));
    } else {
      $this->Authorization->skipAuthorization();
    }
    $destinations = $this->Users->Destinations->find('list', ['limit' => 100, 'order' => 'name']);
    $this->set(compact('user', 'destinations'));
  }

  /**
   * Edit method
   *
   * @param string|null $id User id.
   * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
   * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
   */
  public function edit($id = null)
  {
    $user = $this->Users->get($id, [
      'contain' => [],
    ]);
    $this->Authorization->authorize($user);
    if ($this->request->is(['patch', 'post', 'put'])) {
      $user = $this->Users->patchEntity($user, $this->request->getData());
      $this->Authorization->authorize($user);
      if ($this->Users->save($user)) {
        $this->Flash->success(__('The user has been saved.'));

        return $this->redirect(['action' => 'index']);
      }
      $this->Flash->error(__('The user could not be saved. Please, try again.'));
    }
    $destinations = $this->Users->Destinations->find('list', ['limit' => 100, 'order' => 'name']);
    $this->set(compact('user', 'destinations'));
  }

  /**
   * Delete method
   *
   * @param string|null $id User id.
   * @return \Cake\Http\Response|null|void Redirects to index.
   * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
   */
  public function delete($id = null)
  {
    $this->request->allowMethod(['post', 'delete']);
    $user = $this->Users->get($id);
    $this->Authorization->authorize($user);
    if ($this->Users->delete($user)) {
      $this->Flash->success(__('The user has been deleted.'));
    } else {
      $this->Flash->error(__('The user could not be deleted. Please, try again.'));
    }

    return $this->redirect(['action' => 'index']);
  }
}
