<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Tags Controller
 *
 * @property \App\Model\Table\TagsTable $Tags
 *
 * @method \App\Model\Entity\Tag[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TagsController extends AppController
{
	/**
	 * Index method
	 *
	 * @return \Cake\Http\Response|void
	 */
	public function index() {
		$query = $this->Tags->find();

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
	public function view($id = null) {
		$tag = $this->Tags->get($id, [
			'contain' => ['Articles'],
		]);

		$this->set('tag', $tag);
	}

	/**
	 * Add method
	 *
	 * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
	 */
	public function add() {
		$tag = $this->Tags->newEmptyEntity();
		if ($this->request->is('post')) {
			$tag = $this->Tags->patchEntity($tag, $this->request->getData());
			if ($this->Tags->save($tag)) {
				$this->Flash->success(__('The tag has been saved.'));

				return $this->redirect(['action' => 'index']);
			}
			$this->Flash->error(__('The tag could not be saved. Please, try again.'));
		}
		$articles = $this->Tags->Articles->find('list', ['limit' => 200]);
		$this->set(compact('tag', 'articles'));
	}

	/**
	 * Edit method
	 *
	 * @param string|null $id Tag id.
	 * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 */
	public function edit($id = null) {
		$tag = $this->Tags->get($id, [
			'contain' => ['Articles'],
		]);
		if ($this->request->is(['patch', 'post', 'put'])) {
			$tag = $this->Tags->patchEntity($tag, $this->request->getData());
			if ($this->Tags->save($tag)) {
				$this->Flash->success(__('The tag has been saved.'));

				return $this->redirect(['action' => 'index']);
			}
			$this->Flash->error(__('The tag could not be saved. Please, try again.'));
		}
		$articles = $this->Tags->Articles->find('list', ['limit' => 200]);
		$this->set(compact('tag', 'articles'));
	}

	/**
	 * Delete method
	 *
	 * @param string|null $id Tag id.
	 * @return \Cake\Http\Response|null Redirects to index.
	 * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
	 */
	public function delete($id = null) {
		$this->request->allowMethod(['post', 'delete']);
		$tag = $this->Tags->get($id);
		if ($this->Tags->delete($tag)) {
			$this->Flash->success(__('The tag has been deleted.'));
		} else {
			$this->Flash->error(__('The tag could not be deleted. Please, try again.'));
		}

		return $this->redirect(['action' => 'index']);
	}
}
