<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Filesystem\Folder;
use Cake\Http\Exception\InternalErrorException;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Text;
use elFinder;
use elFinderConnector;

class ArticlesController extends AppController
{
	public function index() {
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
			$query->where(['destination_id' => $destination_id]);
		}

	  //dd($query);
		$this->loadModel('Destinations');
		$destinations = $this->Destinations->find('list')->order('name');
		$this->set('articles', $this->paginate($query, ['limit' => 50]));
		$this->set(compact('destinations', 'q', 'destination_id'));
	}

	public function add() {
		$article = $this->Articles->newEmptyEntity();
		if ($this->request->is('post')) {
			$article = $this->Articles->patchEntity($article, $this->request->getData());

			$article->user_id = $this->Authentication->getIdentity()->getIdentifier();

			if ($this->Articles->save($article)) {
				//Salvare allegati, copertina e galleria
				//Salvare allegati, copertina e galleria
				$error = $article['newcopertina']['error'];
				if ($error == UPLOAD_ERR_OK) {
				  //Prima di caricare la copertina devo cancellare quello che c'è, quindi l'ultimo parametro è TRUE
					$this->uploadFiles($article['id'], 'copertina', [$article['newcopertina']], true);
				} elseif ($error != UPLOAD_ERR_NO_FILE) {
					throw new InternalErrorException($this->phpFileUploadErrors[$error]);
				}

				if (isset($article['newgallery'][0]['error'])){
				$error = $article['newgallery'][0]['error'];
				if ($error == UPLOAD_ERR_OK) {
				  //Prima di caricare la galleria non cancello quello che c'è $errorgià
					$this->uploadFiles($article['id'], 'galleria', $article['newgallery'], false);
				} elseif ($error != UPLOAD_ERR_NO_FILE) {
					throw new InternalErrorException($this->phpFileUploadErrors[$error]);
				}
				}

				if (isset($article['newallegati'][0]['error'])){
				$error = $article['newallegati'][0]['error'];
				if ($error == UPLOAD_ERR_OK) {
					$this->uploadFiles($article['id'], 'files', $article['newallegati'], false);
				} elseif ($error != UPLOAD_ERR_NO_FILE) {
					throw new InternalErrorException($this->phpFileUploadErrors[$error]);
				}
			}

				$this->Flash->success(__('Your article has been saved.'));

				return $this->redirect(['prefix' => false, 'action' => 'view', $article->slug]);
			}
			$this->Flash->error(__('Unable to add your article'));
		  //dd($article->getErrors());
		}
		$tags = $this->Articles->Tags->find('list');
		$users = $this->Articles->Users->find('list', ['keyField' => 'id', 'valueField' => 'username']);
		$destinations = $this->Articles->Destinations->find('list');
		$this->set('user', $this->request->getAttribute('identity')->getIdentifier());
		$this->set(compact('article', 'tags', 'users', 'destinations'));
	}

	public function edit($id) {
		$article = $this->Articles
		->findById($id)
		->contain('Tags')
		->firstOrFail();

		if ($article->destination_id == null) {
			$old_destination = 0;
		} else {
			$old_destination = $article->destination_id;
		}

		//dd($article);

		if ($this->request->is(['post', 'put'])) {
			$this->Articles->patchEntity($article, $this->request->getData());

			if ($this->Articles->save($article)) {
			  //Se hai cambiato destination devo spostare gli allegati nella cartella giusta
				if ($old_destination != $article->destination_id) {
					if ($this->moveAttachments($old_destination, $article->destination_id, $id)) {
						$this->log("Allegati articolo $id spostati con successo dalla cartella {$old_destination} a {$article->destination_id}", 'info');
					} else {
						$this->log("Impossibile spostare gli allegati articolo $id dalla cartella {$old_destination} a {$article->destination_id}", 'error');
					}
				}

			  //Salvare allegati, copertina e galleria
				$error = $article['newcopertina']['error'];
				if ($error == UPLOAD_ERR_OK) {
				  //Prima di caricare la copertina devo cancellare quello che c'è, quindi l'ultimo parametro è TRUE
					$this->uploadFiles($article['id'], 'copertina', [$article['newcopertina']], true);
				} elseif ($error != UPLOAD_ERR_NO_FILE) {
					throw new InternalErrorException($this->phpFileUploadErrors[$error]);
				}

				$error = $article['newgallery'][0]['error'];
				if ($error == UPLOAD_ERR_OK) {
				  //Prima di caricare la galleria non cancello quello che c'è $errorgià
					$this->uploadFiles($article['id'], 'galleria', $article['newgallery'], false);
				} elseif ($error != UPLOAD_ERR_NO_FILE) {
					throw new InternalErrorException($this->phpFileUploadErrors[$error]);
				}

				$error = $article['newallegati'][0]['error'];
			  //dd($error);
			  //dd($article['newallegati'][0]['error']==UPLOAD_ERR_INI_SIZE);
				if ($error == UPLOAD_ERR_OK) {
					$this->uploadFiles($article['id'], 'files', $article['newallegati'], false);
					$this->Flash->success(__('Salvato con successo'));
				} elseif ($error == UPLOAD_ERR_INI_SIZE) {
					$this->Flash->error(__('Dimensione massima superata'));
				} elseif ($error != UPLOAD_ERR_NO_FILE) {
					throw new InternalErrorException($this->phpFileUploadErrors[$error]);
				}
			  //return $this->redirect(['action'=>'view', $article->slug]);
			} else {
				$this->Flash->error(__('Unable to update your article.'));
			}
		}
	  // Get a list of tags.
		$tags = $this->Articles->Tags->find('list');
		$users = $this->Articles->Users->find('list', ['keyField' => 'id', 'valueField' => 'username']);
		$destinations = $this->Articles->Destinations->find('list');
		$this->set('user', $this->request->getAttribute('identity'));
		$this->set(compact('article', 'tags', 'users', 'destinations'));
	}

	public function delete($id) {
		$this->request->allowMethod(['post', 'delete']);

		$article = $this->Articles->findById($id)->firstOrFail();
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

			return $this->redirect(['action' => 'index']);
		}
	}

  ///////////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////////
  ////  FUNZIONI PER GESTIRE FILE ALLEGATI //////////////////////////
  ///////////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////////

	private function getDestinationSlug($article_id) {
		$a = $this->Articles->get($article_id);
		if (!empty($a->destination_id)) {
			$destinations = TableRegistry::getTableLocator()->get('Destinations');

			return $destinations->findById($a->destination_id)->first()->slug . DS;
		} else {
			return null;
		}
	}

	private function uploadFiles($id, $fieldDir, $fnames, $deleteBefore) {
		$copied = false;
	  //this is the folder where i need to save
		$article = $this->Articles->get($id);
		$f = $article->getPath();

		$fullDirTemplate = Configure::read('copertina-pattern', ':sitedir/:model/:destination/:id/:field/');
		$save_dir = Text::insert($fullDirTemplate, [
		'sitedir' => Configure::read('sitedir'),
		'model' => strtolower($article->getSource()),
		'destination' => $this->getDestinationSlug($id),
		'id' => $id,
		'field' => $fieldDir,
		]);

	  //check if $save_dir exists, if not create it
		$folder = new Folder(WWW_ROOT . $save_dir, true, 0777);
		if ($deleteBefore) {
			if ($folder->delete()) {
				// Successfully deleted foo and its nested folders
				$folder = new Folder(WWW_ROOT . $save_dir, true, 0777);
			}
		}
	  //debug($folder);
		$e  = $folder->errors();
		if (!empty($e)) { //$save_dir is a relative path so it is checked relatively to current working directory
			$this->Flash->error("Si è verificato un errore nella creazione della directory. Ripetere l'operazione - " . $e);

			return;
		}
		foreach ($fnames as $fname) {
			$name_on_server = basename($fname["name"]);
			$copied = move_uploaded_file($fname['tmp_name'], WWW_ROOT . $save_dir . DS . $name_on_server);
		}

	  //Se non riesco a spostare nella cartella giusta, esco
		if (!$copied) {
			$toReturn['error'] = 'Si e\' verificato un problema nella creazione dell\'immagine.
				Ripetere l\'inserimento';

			return $toReturn;
		}
	}

	public function removeFile() {
		$fname = $this->request->getQuery('fname');

		if (!empty($fname)) {
			$fname = rtrim(WWW_ROOT, DS) . $fname;
			if (file_exists($fname)) {
				$ip = $_SERVER['REMOTE_ADDR'];
				//TODO: devo cancellare lo stesso nome file anche in tutte le altre cartelle figlie

				unlink($fname);
				$this->log("eliminato il file $fname da $ip");
			} else {
				$this->Flash->error('Il file da eliminare è inesitente:' . $fname);
			}
		}
		$this->redirect(Router::url($this->referer(), true));
	}

  //Quando cambio destination ad un articolo devo spostarea anche gli allegati da una cartella all'altra.

	private function moveAttachments($old_dest, $new_dest, $id) {
		$this->loadModel('Destinations');
		if ($old_dest > 0) {
			$od = $this->Destinations->findById($old_dest)->first();
			if (!empty($od)) {
				$old_dest_name = $od->slug;
			}
		}
		if ($new_dest > 0) {
			$nd = $this->Destinations->findById($new_dest)->first();
			if (!empty($nd)) {
				$new_dest_name = $nd->slug;
			}
		}
		$article = $this->Articles->get($id);
		$path = $article->getPath();

		return @rename($path . $old_dest_name . DS . $id, $path . $new_dest_name . DS . $id);
	}

	public function uploadImage() {
		$r = $this->request->getData();

	  //Salvo le immagini di ckeditor
		$error = $r['upload']['error'];
		if ($error == UPLOAD_ERR_OK) {
			$fname = $r['upload'];
			$fullDirTemplate = ':sitedir/img';
			$save_dir = Text::insert($fullDirTemplate, [
			'sitedir' => Configure::read('sitedir'),
			]);
			$name_on_server = basename($fname["name"]);
			$dest_fname = WWW_ROOT . $save_dir . DS . $name_on_server;
			$copied = move_uploaded_file($fname['tmp_name'], $dest_fname);
		  //Se non riesco a spostare nella cartella giusta, esco
			if (!$copied) {
				  $toReturn['error'] = 'Si e\' verificato un problema nella creazione dell\'immagine.
				Ripetere l\'inserimento';

				  return $toReturn;
			}
			$msg = [
			"uploaded" =>  true,
			"url" =>  "/$save_dir/$name_on_server",
			];
			$this->set([
			'data' => $msg,
			'_serialize' => 'data',
			]);
			$this->RequestHandler->renderAs($this, 'json');
		  //$this->set('msg', $msg);
		  //$this->viewBuilder()->setOption('serialize', ['msg']);
		} elseif ($error != UPLOAD_ERR_NO_FILE) {
			$phpFileUploadErrors = Configure::read('phpFileUploadErrors');
			throw new InternalErrorException($phpFileUploadErrors[$error]);
		}
	}

	public function ckeconnector() {
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
