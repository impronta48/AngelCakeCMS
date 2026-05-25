<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Datasource\Exception\RecordNotFoundException;

/**
 * Tags Controller
 *
 * @property \App\Model\Table\TagsTable $Tags
 *
 * @method \App\Model\Entity\Tag[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TagsController extends AppController
{
	private function saveTag(?int $id = null)
	{
		$tag = $id === null
			? $this->Tags->newEmptyEntity()
			: $this->Tags->get($id, ['contain' => ['TagsEnhancements']]);

		$this->Authorization->authorize($tag);

		if ($this->request->is(['post', 'patch', 'put'])) {
			$tag = $this->Tags->patchEntity($tag, $this->request->getData(), [
				'associated' => ['TagsEnhancements'],
			]);

			if ($this->Tags->save($tag, ['associated' => ['TagsEnhancements']])) {
				$this->Flash->success(__('The tag has been saved.'));

				return $this->redirect(['action' => 'index']);
			}

			$this->Flash->error(__('The tag could not be saved. Please, try again.'));
		}

		$this->set(compact('tag'));

		return $tag;
	}

	/**
	 * Index method
	 *
	 * @return \Cake\Http\Response|void
	 */
	public function index()
	{
		$this->Authorization->skipAuthorization();
		$query = $this->Tags->find()->contain(['Destinations']);
		
		if (!$this->request->is('json')) {
			$tags = $query->all();
		} else {
			$tags = $this->paginate($query);
		}

		$this->set(compact('tags'));
		$this->viewBuilder()->setOption('serialize', 'tags');
	}

	/**
	 * View method
	 *
	 * @param string|null $id Tag id.
	 * @return \Cake\Http\Response|void
	 * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
	 */
	public function view($id = null)
	{
		$tag = $this->Tags->get($id, ['contain' => ['Destinations', 'TagsEnhancements']]);
		$this->Authorization->authorize($tag);
		$this->set('tag', $tag);
	}

	/**
	 * Add method
	 *
	 * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
	 */
	public function add()
	{
		$tag = $this->saveTag();
		$this->set(compact('tag'));
		$this->viewBuilder()->setTemplate('add');
	}

	/**
	 * Edit method
	 *
	 * @param string|null $id Tag id.
	 * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 */
	public function edit($id = null)
	{
		$tag = $this->saveTag((int)$id);
		$this->set(compact('tag'));
		$this->viewBuilder()->setTemplate('edit');
	}

	/**
	 * Delete method
	 *
	 * @param string|null $id Tag id.
	 * @return \Cake\Http\Response|null Redirects to index.
	 * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
	 */
	public function delete($id = null)
	{
		$this->request->allowMethod(['post', 'delete']);
		$tag = $this->Tags->get($id);
		$this->Authorization->authorize($tag);
		if ($this->Tags->delete($tag)) {
			$this->Flash->success(__('The tag has been deleted.'));
		} else {
			$this->Flash->error(__('The tag could not be deleted. Please, try again.'));
		}

		return $this->redirect(['action' => 'index']);
	}
}
