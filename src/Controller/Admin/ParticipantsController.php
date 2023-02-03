<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;

/**
 * Participants Controller
 *
 * @property \App\Model\Table\ParticipantsTable $Participants
 *
 * @method \App\Model\Entity\Participant[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ParticipantsController extends AppController
{

	public $paginate = [
	'contain' => ['Events'],
	];

	public function initialize(): void {
		parent::initialize();
	}

	/**
	 * Index method
	 *
	 * @return \Cake\Http\Response|void
	 */
	public function index($event_id = null) {
		$conditions = [];
		$p = $this->request->getAttribute('params');
		$ext = $p['_ext'];

		if (!empty($event_id)) {
			$conditions['event_id'] = $event_id;
		}

		$query = $this->Participants->find('all', ['conditions' => $conditions]);

		$this->Authorization->applyScope($query);

		if ($ext == 'xls') {
			$participants = $query->toList();
		} else {
			$this->paginate['conditions'] = $conditions;
			$participants = $this->paginate($query);
		}

		$columns = $this->Participants->getSchema()->columns();
		$this->set(compact('participants', 'event_id', 'columns'));
	}

	/**
	 * Edit method
	 *
	 * @param string|null $id Participant id.
	 * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
	 * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
	 */
	public function edit($id = null) {
		$participant = $this->Participants->get($id, [
		'contain' => [],
		]);
		$this->Authorization->authorize($participant);
		if ($this->request->is(['patch', 'post', 'put'])) {
			$participant = $this->Participants->patchEntity($participant, $this->request->getData());
			$this->Authorization->authorize($participant);
			if ($this->Participants->save($participant)) {
				$this->Flash->success(__('The participant has been saved.'));

				return $this->redirect(['action' => 'index']);
			}
			$this->Flash->error(__('The participant could not be saved. Please, try again.'));
		}
		$events = $this->Participants->Events->find('list', ['limit' => 200]);
		$destinations = TableRegistry::getTableLocator()->get('Destinations')->find('list', ['limit' => 200]);
		$this->set(compact('participant', 'events', 'destinations'));
	}

	/**
	 * Delete method
	 *
	 * @param string|null $id Participant id.
	 * @return \Cake\Http\Response|null Redirects to index.
	 * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
	 */
	public function delete($id = null) {
		$this->request->allowMethod(['post', 'delete']);
		$participant = $this->Participants->get($id);
		$this->Authorization->authorize($participant);
		if ($this->Participants->delete($participant)) {
			$this->Flash->success(__('The participant has been deleted.'));
		} else {
			$this->Flash->error(__('The participant could not be deleted. Please, try again.'));
		}

		return $this->redirect(['action' => 'index']);
	}

	/**
	 * View method
	 *
	 * @param string|null $id Participant id.
	 * @return \Cake\Http\Response|void
	 * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
	 */
	public function view($id = null) {
		$participant = $this->Participants->get($id, [
		'contain' => ['Events'],
		]);

		$this->Authorization->authorize($participant);

		$this->set('participant', $participant);
	}
}
