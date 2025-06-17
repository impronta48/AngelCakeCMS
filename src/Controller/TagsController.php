<?php

declare(strict_types=1);


namespace App\Controller\;

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
	public function index()
	{
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
	public function view($id = null)
	{
		$tag = $this->Tags->get($id, [
			'contain' => ['Articles'],
		]);

		$this->set('tag', $tag);
	}

}
