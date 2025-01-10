<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Database\TypeFactory;
use Cake\Core\Configure;

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
	public function index() {
		$q = $this->request->getQuery('q');
		$query = $this->Events->find()
		->contain(['Destinations', 'Percorsi']);

		$this->Authorization->applyScope($query);

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
	public function add() {
		$percorso_id = $this->request->getQuery('percorso_id');

		$event = $this->Events->newEmptyEntity();
		if(!empty($percorso_id)) {
			$percorso = $this->Events->Percorsi->get($percorso_id, [
				'contain' => [],
			]);
			if(!empty($percorso)) {
				$event->title = $percorso->title;
				$event->description = $percorso->descr;
				$event->place = $percorso->comune;
				$event->destination_id = $percorso->destination_id;
				$event->percorso_id = $percorso_id;
				$event->cost = $percorso->a_partire_da_prezzo;
			}
		}
		//Massimoi - lo spegno perchè fa casino con il datetime di firefox
	  	//Necessario per questo https://discourse.cakephp.org/t/patchentity-set-date-field-to-null/7361/3
		//TypeFactory::build('datetime')->useLocaleParser()->setLocaleFormat('yyyy-MM-dd\'T\'HH:mm:ss');
		if ($this->request->is('post')) {
			$event = $this->Events->patchEntity($event, $this->request->getData());
			$this->Authorization->authorize($event);
			if ($this->Events->save($event)) {
				$this->Flash->success(__('The event has been saved.'));

				if($percorso_id) {
					return $this->redirect(['controller' => 'percorsi', 'action' => 'edit', $percorso_id]);
				}
				return $this->redirect(['action' => 'index']);
			}
			$this->Flash->error(__('The event could not be saved. Please, try again.'));
		} else {
			$this->Authorization->skipAuthorization();
		}
		$destinations = $this->destinationsList();
		$percorsi_evento = $this->percorsiEventoList();
		$users = $this->usersList();
		$this->set(compact('event', 'destinations', 'users', 'percorsi_evento', 'percorso_id'));
	}

	private function destinationsList() {
		return $this->Events->Destinations->find('list', ['limit' => 200]);
	}

	private function percorsiEventoList() {
		return $this->Events->Percorsi->find('list', ['conditions' => ['tipo_id' => Configure::read('TipiPercorsi.evento')]]);
	}

	private function usersList() {
		return $this->Events->Users->find('list', ['keyField' => 'id', 'valueField' => 'username']);
	}

	/**
	 * Edit method
	 *
	 * @param string|null $id Event id.
	 * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
	 * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
	 */
	public function edit($id = null) {
		$percorso_id = $this->request->getQuery('percorso_id');

		$event = $this->Events->get($id, [
			'contain' => [],
		]);
		$this->Authorization->authorize($event);
		//Massimoi - lo spegno perchè fa casino con il datetime di firefox
	  	//Necessario per questo https://discourse.cakephp.org/t/patchentity-set-date-field-to-null/7361/3
		//TypeFactory::build('datetime')->useLocaleParser()->setLocaleFormat('yyyy-MM-dd HH:mm');

		if ($this->request->is(['patch', 'post', 'put'])) {
			$event = $this->Events->patchEntity($event, $this->request->getData());
			$this->Authorization->authorize($event);
			if ($this->Events->save($event)) {
				$this->Flash->success(__('The event has been saved.'));

				if($percorso_id) {
					return $this->redirect(['controller' => 'percorsi', 'action' => 'edit', $percorso_id]);
				}
				return $this->redirect(['action' => 'index']);
			} else {
				//log error message
				$e = json_encode($event->getErrors());
				$this->log($e, 'error');	
				$this->Flash->error(__('The event could not be saved. Please, try again.'));
			}
			
		}
		$destinations = $this->destinationsList();
		$percorsi_evento = $this->percorsiEventoList();
		$users = $this->usersList();
		$this->set(compact('event', 'destinations', 'users', 'percorsi_evento', 'percorso_id'));
	}

	/**
	 * Delete method
	 *
	 * @param string|null $id Event id.
	 * @return \Cake\Http\Response|null Redirects to index.
	 * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
	 */
	public function delete($id = null) {
		$percorso_id = $this->request->getQuery('percorso_id');

		$this->request->allowMethod(['post', 'delete']);
		$event = $this->Events->get($id);
		$this->Authorization->authorize($event);
		if ($this->Events->delete($event)) {
			$this->Flash->success(__('The event has been deleted.'));
		} else {
			$this->Flash->error(__('The event could not be deleted. Please, try again.'));
		}

		if($percorso_id) {
			return $this->redirect(['controller' => 'percorsi', 'action' => 'edit', $percorso_id]);
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
	public function view($id = null) {
		$event = $this->Events->get($id, [
		'contain' => ['Destinations', 'Users', 'Participants', 'Percorsi'],
		]);

		$this->Authorization->authorize($event);

		$this->set('event', $event);
	}
}
