<?php
namespace App\Controller;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Filesystem\Folder;
use Cake\Routing\Router;
use App\Model\Entity\Article;

class ArticlesController extends AppController
{
	public function initialize(): void
	{
		parent::initialize();

		$this->loadComponent('Paginator');
		//$this->Authentication->allowUnauthenticated(['getList','index','view']);
	}

	public function admin()
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
			->order(['Articles.id' =>'DESC']);

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
		$this->set('articles', $this->paginate($query));
		$this->set(compact('destinations','q','destination_id'));
	}

	public function view($slug = null)
	{
		$article = $this->Articles->findBySlug($slug)
				->contain(['Tags'])
				->firstOrFail();
		$this->set(compact('article'));
		$this->set('user',  $this->request->getAttribute('identity'));
	}

	public function add()
	{
		$article = $this->Articles->newEmptyEntity();
		if ($this->request->is('post'))
		{
			$article = $this->Articles->patchEntity($article, $this->request->getData());

			$article->user_id = $this->Authentication->getIdentity()->getIdentifier();;
			if ($this->Articles->save($article)) {
				//dd($article);
				//Salvare allegati, copertina e galleria
				if (!$article['newcopertina']['error'] == UPLOAD_ERR_NO_FILE)
				{
					//Prima di caricare la copertina devo cancellare quello che c'è, quindi l'ultimo parametro è TRUE
					$this->uploadFiles($article['id'],'copertina',[$article['newcopertina']],true);
				}
				if (!$article['newgallery'][0]['error'] == UPLOAD_ERR_NO_FILE)
				{
					//Prima di caricare la copertina devo cancellare quello che c'è, quindi l'ultimo parametro è TRUE
					$this->uploadFiles($article['id'],'galleria',$article['newgallery'],false);
				}
				if (!$article['newallegati'][0]['error']==UPLOAD_ERR_NO_FILE)
				{
					//Prima di caricare la copertina devo cancellare quello che c'è, quindi l'ultimo parametro è TRUE
					$this->uploadFiles($article['id'],'files',$article['newallegati'],false);
				}
				$this->Flash->success(__('Your article has been saved.'));
				return $this->redirect(['action'=>'view', $article->slug]);
			}
			$this->Flash->error(__('Unable to add your article'));
			dd($article->getErrors());
		}
		$tags = $this->Articles->Tags->find('list');
		$users = $this->Articles->Users->find('list',['keyField' => 'id', 'valueField' => 'username']);
		$destinations = $this->Articles->Destinations->find('list');
		$this->set(compact('article', 'tags','users','destinations'));
	}

	public function edit($id)
	{
		$article = $this->Articles
				->findById($id)
				->contain('Tags')
				->firstOrFail();

		if ($article->destination_id == null)
		{
			$old_destination = 0;
		}
		else{
			$old_destination = $article->destination_id;
		}

		if ($this->request->is(['post','put']))
		{
			$this->Articles->patchEntity($article,$this->request->getData());

			if ($this->Articles->save($article)) {

				//Se hai cambiato destination devo spostare gli allegati nella cartella giusta
				if( $old_destination != $article->destination_id)
				{
						if ($this->moveAttachments($old_destination, $article->destination_id, $id))
						{
							$this->log("Allegati articolo $id spostati con successo dalla cartella {$old_destination} a {$article->destination_id}", 'info');
						}
						else {
							$this->log("Impossibile spostare gli allegati articolo $id dalla cartella {$old_destination} a {$article->destination_id}", 'error');
						}
				}

				//Salvare allegati, copertina e galleria
				if (!$article['newcopertina']['error'] == UPLOAD_ERR_NO_FILE)
				{
					//Prima di caricare la copertina devo cancellare quello che c'è, quindi l'ultimo parametro è TRUE
					$this->uploadFiles($article['id'],'copertina',[$article['newcopertina']],true);
				}
				if (!$article['newgallery'][0]['error'] == UPLOAD_ERR_NO_FILE)
				{
					//Prima di caricare la galleria non cancello quello che c'è già
					$this->uploadFiles($article['id'],'galleria',$article['newgallery'],false);
				}
				if (!$article['newallegati'][0]['error']==UPLOAD_ERR_NO_FILE)
				{
					$this->uploadFiles($article['id'],'files',$article['newallegati'],false);
				}
				$this->Flash->success(__('Salvato con successo'));
				//return $this->redirect(['action'=>'view', $article->slug]);
			}
			else{
				$this->Flash->error(__('Unable to update your article.'));
			}
		}
		// Get a list of tags.
    	$tags = $this->Articles->Tags->find('list');
		$users = $this->Articles->Users->find('list',['keyField' => 'id', 'valueField' => 'username']);
    	$destinations = $this->Articles->Destinations->find('list');
		$this->set(compact('article','tags','users','destinations'));
	}


	public function delete($id)
	{
		$this->request->allowMethod(['post','delete']);

		$article = $this->Articles->findById($id)->firstOrFail();
		$dest= $this->getDestinationSlug($id);
		if ($this->Articles->delete($article))
		{
			$f = $this->getPath();
			$save_dir = $f . $dest . $id ;

			//Cancellare anche la cartella degli allegati
			$folder = new Folder($save_dir, true, 0777);
		  if (!$folder->delete()) {
					// Successfully deleted foo and its nested folders
					$this->log('impossibile cancellare il folder:' . $save_dir);
			}

			$this->Flash->success(__('The {0} article has been deleted.', $article->title));
			return $this->redirect(['action'=>'index']);
		}
	}

	public function tags()
	{
		$tags = $this->request->getParam('pass');

		$articles = $this->Articles->find('tagged',
			['tags' => $tags]
		);

		$this->set(compact('articles','tags'));
	}

	///////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////
	////	FUNZIONI PER GESTIRE FILE ALLEGATI //////////////////////////
	///////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////

	private function getPath()
	{
		$sitedir = Configure::read('sitedir');
		return WWW_ROOT .  $sitedir . '/articles/' ;
	}

	private function getUrl()
	{
		$sitedir = Configure::read('sitedir');
		return  Router::url('/') . $sitedir . '/articles/' ;
	}

	private function getDestinationSlug($article_id)
	{
		$a = $this->Articles->get($article_id);
		if (!empty($a->destination_id))
		{
			$destinations = TableRegistry::getTableLocator()->get('Destinations');
			return $destinations->findById($a->destination_id)->first()->slug . DS;
		}
		else {
			return null;
		}
	}

	private function uploadFiles($id,$fieldDir,$fnames,$deleteBefore){

		//this is the folder where i need to save
		$f = $this->getPath();
		$dest= $this->getDestinationSlug($id);

		$save_dir = $f . $dest . $id . DS . $fieldDir;

		//check if $save_dir exists, if not create it
		$folder = new Folder($save_dir, true, 0777);
		if ($deleteBefore)
		{
			if ($folder->delete()) {
				// Successfully deleted foo and its nested folders
				$folder = new Folder($save_dir, true, 0777);
			}
		}
		//debug($folder);
		$e  = $folder->errors();
		if(!empty($e)) //$save_dir is a relative path so it is checked relatively to current working directory
		{
			$this->Flash->error( "Si è verificato un errore nella creazione della directory. Ripetere l'operazione - " . $e );
			return;
		}
		foreach ($fnames as $fname)
		{
			$name_on_server = basename($fname["name"]);
			$copied = move_uploaded_file($fname['tmp_name'], $save_dir.DS.$name_on_server);
		}

		//Se non riesco a spostare nella cartella giusta, esco
		if(!$copied)
		{
			$toReturn['error'] = 'Si e\' verificato un problema nella creazione dell\'immagine.
				Ripetere l\'inserimento';
			return $toReturn;
		}
	}


	public function removeFile(){
		$fname = $this->request->getQuery('fname');

		if (!empty($fname)){
			$fname = rtrim(WWW_ROOT,DS) . $fname;
			if (file_exists($fname))
			{
				$ip =$_SERVER['REMOTE_ADDR'];
				//TODO: devo cancellare lo stesso nome file anche in tutte le altre cartelle figlie

				unlink($fname);
				$this->log("eliminato il file $fname da $ip");
			}
			else {
				$this->Flash->error('Il file da eliminare è inesitente:' . $fname);
			}
		}
		$this->redirect(Router::url( $this->referer(), true ) );
	}

	//Quando cambio destination ad un articolo devo spostarea anche gli allegati da una cartella all'altra.
	private function moveAttachments($old_dest, $new_dest, $id)
	{
		$this->loadModel('Destinations');
		$old_dest_name = $this->Destinations->findById($old_dest)->first()->slug;
		$new_dest_name = $this->Destinations->findById($new_dest)->first()->slug;
		$path = $this->getPath();
		return rename($path . $old_dest_name . DS. $id, $path . $new_dest_name . DS . $id );

	}
}