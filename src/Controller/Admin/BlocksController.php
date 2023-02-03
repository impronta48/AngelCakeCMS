<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;

/**
 * Blocks Controller
 *
 * @property \App\Model\Table\BlocksTable $Blocks
 * @method \App\Model\Entity\Block[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class BlocksController extends AppController
{
	/**
	 * Index method
	 *
	 * @return \Cake\Http\Response|null|void Renders view
	 */
	public function index() {
		$query = $this->Blocks->find();

		$this->Authorization->applyScope($query);

		$blocks = $this->paginate($query);

		$this->set(compact('blocks'));
	}

	/**
	 * Add method
	 *
	 * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
	 */
	public function add() {
		$block = $this->Blocks->newEmptyEntity();
		if ($this->request->is('post')) {
			$block = $this->Blocks->patchEntity($block, $this->request->getData());
			$this->Authorization->authorize($block);
			if ($this->Blocks->save($block)) {
				$this->Flash->success(__('The block has been saved.'));

				return $this->redirect(['action' => 'index']);
			}
			$this->Flash->error(__('The block could not be saved. Please, try again.'));
		} else {
			$this->Authorization->skipAuthorization();
		}
		$this->set(compact('block'));
	}

	/**
	 * Edit method
	 *
	 * @param string|null $id Block id.
	 * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
	 * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
	 */
	public function edit($id = null) {
		$block = $this->Blocks->get($id, [
		'contain' => [],
		]);
		$this->Authorization->authorize($block);
		if ($this->request->is(['patch', 'post', 'put'])) {
			$block = $this->Blocks->patchEntity($block, $this->request->getData());
			$this->Authorization->authorize($block);
			if ($this->Blocks->save($block)) {
				$this->Flash->success(__('The block has been saved.'));

				$r = $this->request->getQuery('referrer');
				if (!empty($r)) {
					return $this->redirect($r);
				}

				return $this->redirect(['action' => 'index']);
			}
			$this->Flash->error(__('The block could not be saved. Please, try again.'));
		}
		$this->set(compact('block'));
	}

	/**
	 * Delete method
	 *
	 * @param string|null $id Block id.
	 * @return \Cake\Http\Response|null|void Redirects to index.
	 * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
	 */
	public function delete($id = null) {
		$this->request->allowMethod(['post', 'delete']);
		$block = $this->Blocks->get($id);
		$this->Authorization->authorize($block);
		if ($this->Blocks->delete($block)) {
			$this->Flash->success(__('The block has been deleted.'));
		} else {
			$this->Flash->error(__('The block could not be deleted. Please, try again.'));
		}

		return $this->redirect(['action' => 'index']);
	}
}
