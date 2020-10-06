<?php

namespace App\Controller;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Filesystem\Folder;
use Cake\Routing\Router;
use App\Model\Entity\Article;
use Cake\Http\Exception\InternalErrorException;
use Cake\Utility\Text;

class ArticlesController extends AppController
{
  public $paginate = [
    'limit' => 5,
  ];
  private $phpFileUploadErrors;

  public function initialize(): void
  {
    $this->phpFileUploadErrors = [
      0 => 'There is no error, the file uploaded with success',
      1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini: ' . ini_get("upload_max_filesize"),
      2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
      3 => 'The uploaded file was only partially uploaded',
      4 => 'No file was uploaded',
      6 => 'Missing a temporary folder',
      7 => 'Failed to write file to disk.',
      8 => 'A PHP extension stopped the file upload.',
    ];

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

  public function view($slug = null)
  {
    $article = $this->Articles->findBySlug($slug)
      ->contain(['Tags', 'Destinations'])
      ->firstOrFail();
    $this->set(compact('article'));
    $this->set('user',  $this->request->getAttribute('identity'));
  }

  public function add()
  {
    $article = $this->Articles->newEmptyEntity();
    if ($this->request->is('post')) {
      $article = $this->Articles->patchEntity($article, $this->request->getData());

      $article->user_id = $this->Authentication->getIdentity()->getIdentifier();;
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

        $error = $article['newgallery'][0]['error'];
        if ($error == UPLOAD_ERR_OK) {
          //Prima di caricare la galleria non cancello quello che c'è $errorgià
          $this->uploadFiles($article['id'], 'galleria', $article['newgallery'], false);
        } elseif ($error != UPLOAD_ERR_NO_FILE) {
          throw new InternalErrorException($this->phpFileUploadErrors[$error]);
        }

        $error = $article['newallegati'][0]['error'];
        if ($error == UPLOAD_ERR_OK) {
          $this->uploadFiles($article['id'], 'files', $article['newallegati'], false);
        } elseif ($error != UPLOAD_ERR_NO_FILE) {
          throw new InternalErrorException($this->phpFileUploadErrors[$error]);
        }

        $this->Flash->success(__('Your article has been saved.'));
        return $this->redirect(['action' => 'view', $article->slug]);
      }
      $this->Flash->error(__('Unable to add your article'));
      dd($article->getErrors());
    }
    $tags = $this->Articles->Tags->find('list');
    $users = $this->Articles->Users->find('list', ['keyField' => 'id', 'valueField' => 'username']);
    $destinations = $this->Articles->Destinations->find('list');
    $this->set('user',  $this->request->getAttribute('identity')->getIdentifier());
    $this->set(compact('article', 'tags', 'users', 'destinations'));
  }

  public function edit($id)
  {
    $article = $this->Articles
      ->findById($id)
      ->contain('Tags')
      ->firstOrFail();

    if ($article->destination_id == null) {
      $old_destination = 0;
    } else {
      $old_destination = $article->destination_id;
    }

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
    $this->set('user',  $this->request->getAttribute('identity'));
    $this->set(compact('article', 'tags', 'users', 'destinations'));
  }


  public function delete($id)
  {
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

  public function tags()
  {
    $tags = $this->request->getParam('pass');

    $articles = $this->Articles->find(
      'tagged',
      ['tags' => $tags]
    );

    $this->set(compact('articles', 'tags'));
  }

  ///////////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////////
  ////	FUNZIONI PER GESTIRE FILE ALLEGATI //////////////////////////
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

  private function uploadFiles($id, $fieldDir, $fnames, $deleteBefore)
  {

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
    if (!empty($e)) //$save_dir is a relative path so it is checked relatively to current working directory
    {
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


  public function removeFile()
  {
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
  private function moveAttachments($old_dest, $new_dest, $id)
  {
    $this->loadModel('Destinations');
    $old_dest_name = $this->Destinations->findById($old_dest)->first()->slug;
    $new_dest_name = $this->Destinations->findById($new_dest)->first()->slug;
    $article = $this->Articles->get($id);
    $path = $article->getPath();
    return rename($path . $old_dest_name . DS . $id, $path . $new_dest_name . DS . $id);
  }


  /**
   * Index method.
   *
   * @return \Cake\Http\Response|void|null Renders view
   */
  public function index()
  {

    $query = $this->Articles->find()
      ->where(['published' => 1])
      ->order(['Articles.modified DESC']);

    $promoted = $this->request->getQuery('promoted');
    if ($promoted) {
      $query->where(['promoted' => 1]);
    }
    $archived = $this->request->getQuery('archived');
    if ($archived) {
      $query->where(['archived' => 1]);
    }
    /*else
		{
			$query->where(['archived' => 0]);
		}*/
    $slider = $this->request->getQuery('slider');
    if ($slider) {
      $query->where(['slider' => 1]);
    }
    $month = $this->request->getQuery('month');
    if ($month) {
      $query->where(['MONTH(Articles.modified)' => $month]);
    }
    $year = $this->request->getQuery('year');
    if ($year) {
      $query->where(['YEAR(Articles.modified)' => $year]);
    }
    $projects = $this->request->getQuery('projects');
    if ($projects) {
      $query->contain('Projects');
      $query->contain(['Destinations' => ['fields' => ['name', 'slug']]]);
      $query->innerJoinWith('Projects');
    }

    $destinations = [];
    $destination_id = $this->request->getQuery('destination_id');
    //dd($destination_id);
    if ($destination_id) {
      //dd($destination_id);
      $destinations = $this->Articles->Destinations->find()
        ->select('name', 'slug')
        ->where(['id IN' => $destination_id])
        ->toList();

      if (is_array($destination_id)) {
        $query->where(['destination_id IN' => $destination_id]);
      } else {
        $query->where(['destination_id' => $destination_id]);
      }
      $query->contain(['Destinations' => ['fields' => ['name', 'slug']]]);
    }

    $articles = $this->paginate($query);
    $pagination = $this->Paginator->getPagingParams();
    $this->set(compact('articles', 'pagination', 'destinations'));
    $this->viewBuilder()->setOption('serialize', ['articles', 'pagination', 'destinations']);
  }

  public function search()
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


    //Faccio la query di base (tira su tutti gli articoli)
    $query = $this->Articles->find()
      ->contain(['Users', 'Destinations'])
      ->order(['Articles.id' => 'DESC']);

    //Se mi hai passato dei parametri in query filtro su quelli
    //filtro sia su body che su title
    if (!empty($q)) {
      //$query->where(['body LIKE' => "%$q%"])
      //->or_(['title LIKE' => "%$q%"]);
      $query->where(['OR' => ['body LIKE' => "%$q%", 'title LIKE' => "%$q%"]]);
    }
    $articles = $this->paginate($query);
    $pagination = $this->Paginator->getPagingParams();
    $this->set(compact('articles', 'pagination', 'q'));
    $this->viewBuilder()->setOption('serialize', ['articles', 'pagination', 'q']);
  }

  public function getMonthYear()
  {
    /*$query= "SELECT COUNT(id), MONTH(modified), YEAR(modified)
		FROM articles
		GROUP BY MONTH(modified), YEAR(modified)";*/
    //$this->autoRender=false;

    $query = $this->Articles->find();
    $query->select([
      'nArticoli' => $query->func()->count('MONTH(Articles.modified)'),
      'mese' => 'MONTH(Articles.modified)',
      'anno' => 'YEAR(Articles.modified)'
    ])
      ->group(['YEAR(Articles.modified)', 'MONTH(Articles.modified)'])
      ->where(['Articles.published' => 1]);

    $destination_id = $this->request->getQuery('destination_id');
    if (!empty($destination_id)) {
      if (is_array($destination_id)) {
        $query->where(['destination_id IN' => $destination_id]);
      } else {
        $query->where(['destination_id' => $destination_id]);
      }
    }

    $monthyear = $this->paginate($query);
    //dd($monthyear);
    $this->set(compact('monthyear'));
    $this->viewBuilder()->setOption('serialize', ['monthyear']);
  }
}
