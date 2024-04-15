<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;
use App\Lib\AttachmentManager;
use Cake\Routing\Router;
use Psr\Log\LogLevel;
use Cake\Event\EventInterface;
use Cake\Core\Configure;
use Cake\Filesystem\Folder;

/**
 * Destinations Controller
 *
 * @property \App\Model\Table\DestinationsTable $Destinations
 *
 * @method \App\Model\Entity\Destination[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class DestinationsController extends AppController
{

	public function beforeRender(EventInterface $event)
	{
		$theme = Configure::read('theme');
		$this->viewBuilder()->setTheme($theme);
	}

	public function index()
	{
		
		$query = $this->Destinations->find()
			->contain(['Articles']);
		//Se mi hai passato dei parametri in query filtro su quelli
		$q = $this->request->getQuery('q');
		if (!empty($q)) {
			$query->where(['name LIKE' => "%$q%"]);
		}
		
		$this->Authorization->applyScope($query);

		$destinations = $this->paginate($query);

		$this->set(compact('destinations','q'));
	}

	/**
	 * View method
	 *
	 * @param string|null $id Destination id.
	 * @return \Cake\Http\Response|void
	 * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
	 */
	public function view($id = null)
	{
		$query = $this->Destinations->find();
		$a = $this->request->getQuery('archive');

		$articles_q = $this->Destinations->Articles->find()
			->order(['modified' => 'DESC']);

		if (empty($a)) {
			$articles_q->where(['Articles.archived' => false]);
		} else {
			$articles_q->where(['Articles.archived' => true]);
		}

		if (is_string($id)) {
			try {
				$id = $this->Destinations->findBySlug($id)->firstOrFail()->id;
			} catch (\Cake\Datasource\Exception\RecordNotFoundException $ex) {
				$this->log(sprintf('Record not found in database (id = %d)!', $id), LogLevel::WARNING);
			}
		}

		$query->where(['id' => $id]);                 //Filtro le destination
		$destination = $query->first();

		$this->Authorization->authorize($destination);

		$this->set('destination', $destination);

		$articles_q->where(['destination_id' => $id]); //Filtro gli articoli
		$articles_q->where(['published' => true]); //Mostro solo quelli pubblicati
		$art = $this->paginate($articles_q);
		$this->set('articles', $art);

		$this->set('archived', $a);
	}

	/**
	 * Add method
	 *
	 * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
	 */
	public function add()
	{
		$this->set('new', true);
    	$this->edit();
    	$this->viewBuilder()->setTemplate('edit');
	}

	/**
	 * Edit method
	 *
	 * @param string|null $id Destination id.
	 * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
	 * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
	 */
	public function edit($id = null)
	{
		if (is_null($id)) {
			$destination = $this->Destinations->newEmptyEntity();
		} else {
			$destination = $this->Destinations->get($id);
		}
		$new = $destination->isNew();
		$this->Authorization->authorize($destination);

		if ($this->request->is(['patch', 'post', 'put'])) {
			$data = $this->request->getData();
			$destination = $this->Destinations->patchEntity($destination, $data);
			$this->Authorization->authorize($destination);

			if ($this->Destinations->save($destination)) {
				$destination = $this->Destinations->get($destination->id);
				$upload_session_fields = $this->request->getData('upload_session_id');
				if ($new && !empty($upload_session_fields)) {
					foreach ($upload_session_fields as $ses) {
						if (!empty($ses)) {
							$ses = explode('|', $ses);
							$tmpath = AttachmentManager::buildPath('Destinations', 'TEMP', $ses[0], $ses[1]);
							if (is_dir(TMP . $tmpath)) {
								$finalpath = AttachmentManager::buildPath('Destinations', $destination->slug, $destination->id, $ses[1]);
								$dir = new Folder(WWW_ROOT . $finalpath, true);
								rename(TMP . $tmpath, WWW_ROOT . $finalpath);
							}
						}
					}
				}
				$this->Flash->success(__('The percorso has been saved.'));
				return $this->redirect(['prefix' => false, 'action' => 'view', $destination->id]);
			}
			$this->Flash->error(__('The percorso could not be saved. Please, try again.'));
		}

		$this->set('destination', $destination);
		$this->set('new', $new);
	}

	/**
	 * Delete method
	 *
	 * @param string|null $id Destination id.
	 * @return \Cake\Http\Response|null Redirects to index.
	 * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
	 */
	public function delete($id = null)
	{		
		$destination = $this->Destinations->get($id);
		$this->Authorization->authorize($destination);

		if ($this->Destinations->delete($destination)) {
			$this->Flash->success(__('The destination has been deleted.'));
		} else {
			$this->Flash->error(__('The destination could not be deleted. Please, try again.'));
		}

		$this->redirect(Router::url($this->referer(), true));
	}
}
