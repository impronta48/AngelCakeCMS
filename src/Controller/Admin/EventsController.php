<?php

namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Database\TypeFactory;
use Cake\Event\Event;

/**
 * Events Controller
 *
 * @property \App\Model\Table\EventsTable $Events
 *
 * @method \App\Model\Entity\Event[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class EventsController extends AppController
{


  /**
   * Index method
   *
   * @return \Cake\Http\Response|void
   */
  public function index()
  {
    $q = $this->request->getQuery('q');
    $query = $this->Events->find()
      ->contain(['Destinations']);
    //Se mi hai passato dei parametri in query filtro su quelli
    if (!empty($q)) {
      $query->where(['title LIKE' => "%$q%"]);
    }
    $events = $this->paginate($query);

    $this->set(compact('events', 'q'));
  }


  /**
   * Add method
   *
   * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
   */
  public function add()
  {
    $event = $this->Events->newEmptyEntity();
    //Necessario per questo https://discourse.cakephp.org/t/patchentity-set-date-field-to-null/7361/3
    TypeFactory::build('datetime')->useLocaleParser()->setLocaleFormat('yyyy-MM-dd\'T\'HH:mm:ss');
    if ($this->request->is('post')) {
      $event = $this->Events->patchEntity($event, $this->request->getData());

      if ($this->Events->save($event)) {
        $this->Flash->success(__('The event has been saved.'));

        return $this->redirect(['action' => 'index']);
      }
      $this->Flash->error(__('The event could not be saved. Please, try again.'));
    }
    $destinations = $this->Events->Destinations->find('list', ['limit' => 200]);
    $users = $this->Events->Users->find('list', ['keyField' => 'id', 'valueField' => 'username']);
    $this->set(compact('event', 'destinations', 'users'));
  }

  /**
   * Edit method
   *
   * @param string|null $id Event id.
   * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
   * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
   */
  public function edit($id = null)
  {
    $event = $this->Events->get($id, [
      'contain' => []
    ]);
    //Necessario per questo https://discourse.cakephp.org/t/patchentity-set-date-field-to-null/7361/3
    TypeFactory::build('datetime')->useLocaleParser()->setLocaleFormat('yyyy-MM-dd\'T\'HH:mm:ss');

    if ($this->request->is(['patch', 'post', 'put'])) {
      $event = $this->Events->patchEntity($event, $this->request->getData());

      if ($this->Events->save($event)) {
        $this->Flash->success(__('The event has been saved.'));

        return $this->redirect(['action' => 'index']);
      }
      $this->Flash->error(__('The event could not be saved. Please, try again.'));
    }
    $destinations = $this->Events->Destinations->find('list', ['limit' => 200]);
    $users = $this->Events->Users->find('list', ['keyField' => 'id', 'valueField' => 'username']);
    $this->set(compact('event', 'destinations', 'users'));
  }

  /**
   * Delete method
   *
   * @param string|null $id Event id.
   * @return \Cake\Http\Response|null Redirects to index.
   * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
   */
  public function delete($id = null)
  {
    $this->request->allowMethod(['post', 'delete']);
    $event = $this->Events->get($id);
    if ($this->Events->delete($event)) {
      $this->Flash->success(__('The event has been deleted.'));
    } else {
      $this->Flash->error(__('The event could not be deleted. Please, try again.'));
    }

    return $this->redirect(['action' => 'index']);
  }

  /**
   * View method
   *
   * @param string|null $id Event id.
   * @return \Cake\Http\Response|void
   * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
   */
  public function view($id = null)
  {
    $event = $this->Events->get($id, [
      'contain' => ['Destinations', 'Users', 'Participants']
    ]);

    $this->set('event', $event);
  }
}
