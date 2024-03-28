<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;
use App\Lib\AttachmentManager;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Filesystem\Folder;
use Cake\ORM\TableRegistry;
use elFinder;
use elFinderConnector;

class ArticlesController extends AppController
{
	public function index()
	{
		$this->loadComponent('Paginator');

		//Prima della cura: Trova tutti gli articoli
		//Se l'utente ha compilato il form che ha valorizzato q="dav"
		//Allora imposto una condizione della query
		//Dopo la cura: Trova tutti gli articoli WHERE titolo like %dav% OR body like %dav%
		//per fare questo creo un array vuoto che si chiama $conditions
		//$conditions = [];
		//Se this->request->query('q') non è vuoto
		//imposto le conditions come si deve,
		//$conditions['title LIKE'] = "%$q%";
		//$conditions['body LIKE'] = "%$q%";
		//Passo le conditions alla find find(['conditions'=>$conditions]);

		//Leggo i valori dalla querystring (quella che sta dopo il ? nell'url)
		$q = $this->request->getQuery('q');
		$destination_id = $this->request->getQuery('destination_id');

		//Faccio la query di base (tira su tutti gli articoli)
		$query = $this->Articles->find()
			->contain(['Users', 'Destinations'])
			->order(['Articles.id' => 'DESC']);

		//Se mi hai passato dei parametri in query filtro su quelli
		if (!empty($q)) {
			$query->where(['title LIKE' => "%$q%"]);
		}
		if (!empty($destination_id)) {
			$query->where(['Articles.destination_id' => $destination_id]);
		}

		$this->Authorization->applyScope($query);

		//dd($query);
		$this->loadModel('Destinations');
		$destinations = $this->Destinations->find('list')->order('name');
		$this->set('articles', $this->paginate($query, ['limit' => 50]));
		$this->set(compact('destinations', 'q', 'destination_id'));
	}

	public function add()
	{
		$article = $this->Articles->newEmptyEntity();
		if ($this->request->is('post')) {
			$article = $this->Articles->patchEntity($article, $this->request->getData());

			$article->user_id = $this->Authentication->getIdentity()->getIdentifier();

			$this->Authorization->authorize($article);

			if ($this->Articles->save($article)) {
				$this->Flash->success(__('Your article has been saved.'));
				Cache::clear('_cake_routes_');
				return $this->redirect(['prefix' => false, 'action' => 'view', $article->slug]);
			}
			$this->Flash->error(__('Unable to add your article'));
			//dd($article->getErrors());
		} else {
			$this->Authorization->skipAuthorization();
		}

		$tags = $this->Articles->Tags->find('list');
		$users = $this->Articles->Users->find('list', ['keyField' => 'id', 'valueField' => 'username']);
		$destinations = $this->Articles->Destinations->find('list');
		$new = true;
		$this->set('user', $this->request->getAttribute('identity')->getIdentifier());
		$this->set(compact('new', 'article', 'tags', 'users', 'destinations'));
	}

	public function edit($id)
	{
		$article = $this->Articles
			->findById($id)
			->contain(['Tags', 'Destinations'])
			->firstOrFail();

		$this->Authorization->authorize($article);

		if ($article->destination_id == null) {
			$old_destination = 0;
		} else {
			$old_destination = $article->destination_id;
		}

		//dd($article);

		if ($this->request->is(['post', 'put'])) {

			$old_copertina = [$article->copertina];
			$old_galleria = $article->galleria;
			$old_allegati = $article->allegati;

			$this->Articles->patchEntity($article, $this->request->getData());

			$this->Authorization->authorize($article);

			if ($this->Articles->save($article)) {
				//Importante questo è necessario altrimenti non si aggiorna la route cache
				Cache::clear('_cake_routes_');

				//Se hai cambiato destination devo spostare gli allegati nella cartella giusta
				if ($old_destination != $article->destination_id) {
					if (
						AttachmentManager::moveAllFiles($old_copertina, $article->getSource(), $article->getDestinationSlug(), $article->id, 'copertina') &&
						AttachmentManager::moveAllFiles($old_galleria, $article->getSource(), $article->getDestinationSlug(), $article->id, 'galleria') &&
						AttachmentManager::moveAllFiles($old_allegati, $article->getSource(), $article->getDestinationSlug(), $article->id, 'allegati')
					) {
						$this->log("Allegati articolo $id spostati con successo dalla cartella {$old_destination} a {$article->destination_id}", 'info');
					} else {
						$this->log("Impossibile spostare gli allegati articolo $id dalla cartella {$old_destination} a {$article->destination_id}", 'error');
					}
				}

			} else {
				$this->Flash->error(__('Unable to update your article.'));
			}
		}
		// Get a list of tags.
		$tags = $this->Articles->Tags->find('list');
		$users = $this->Articles->Users->find('list', ['keyField' => 'id', 'valueField' => 'username']);
		$destinations = $this->Articles->Destinations->find('list');
		$new = false;
		$this->set('user', $this->request->getAttribute('identity'));
		$this->set(compact('new', 'article', 'tags', 'users', 'destinations'));
	}

	public function delete($id)
	{
		$this->request->allowMethod(['post', 'delete']);

		$article = $this->Articles->findById($id)->firstOrFail();
		$this->Authorization->authorize($article);

		$dest = $this->getDestinationSlug($id);
		if ($this->Articles->delete($article)) {
			$f = $article->getPath();
			$save_dir = $f . $dest . $id;

			//Cancellare anche la cartella degli allegati
			$folder = new Folder($save_dir, true, 0777);
			if (!$folder->delete()) {
				// Successfully deleted foo and its nested folders
				$this->log('impossibile cancellare il folder:' . $save_dir);
			}

			$this->Flash->success(__('The {0} article has been deleted.', $article->title));
			Cache::clear('_cake_routes_');
			return $this->redirect(['action' => 'index']);
		}
	}

	///////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////
	////  FUNZIONI PER GESTIRE FILE ALLEGATI //////////////////////////
	///////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////

	private function getDestinationSlug($article_id)
	{
		$a = $this->Articles->get($article_id);
		if (!empty($a->destination_id)) {
			$destinations = TableRegistry::getTableLocator()->get('Destinations');

			return $destinations->findById($a->destination_id)->first()->slug . DS;
		} else {
			return null;
		}
	}

	public function ckeconnector()
	{
		$opts = [
			'debug' => false,
			'roots' => [
				// Items volume
				[
					'driver'        => 'LocalFileSystem',           // driver for accessing file system (REQUIRED)
					'path'          =>  WWW_ROOT . Configure::read('sitedir') . '/' . 'attachments', // path to files (REQUIRED)
					'URL'           => '/' . Configure::read('sitedir') . '/' . 'attachments', // URL to files (REQUIRED)
					'winHashFix'    => DIRECTORY_SEPARATOR !== '/', // to make hash same to Linux one on windows too
					'uploadDeny'    => ['all'],                // All Mimetypes not allowed to upload
					'uploadAllow'   => [
						'image/x-ms-bmp', 'image/gif', 'image/jpeg', 'image/png', 'image/x-icon',
						'text/plain', 'application/pdf',
						'application/msword',
						'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
						'application/vnd.ms-excel',
						'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
						'application/vnd.ms-powerpoint',
						'application/vnd.openxmlformats-officedocument.presentationml.presentation',
						'application/vnd.oasis.opendocument.text',
						'application/vnd.oasis.opendocument.spreadsheet',
						'application/vnd.oasis.opendocument.presentation',
					], // Mimetype `image` and `text/plain` allowed to upload
					'uploadOrder'   => ['deny', 'allow'],      // allowed Mimetype `image` and `text/plain` only
					'accessControl' => 'access',                     // disable and hide dot starting files (OPTIONAL)
				],
			],
		];

		// run elFinder

		$this->autoRender = false;
		$connector = new elFinderConnector(new elFinder($opts));
		$connector->run();
	}
}
