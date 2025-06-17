<?php

declare(strict_types=1);

namespace App\Controller;

class ArticlesController extends AppController
{

  public $paginate = [
    'limit' => 12,
  ];

  public function initialize(): void
  {
    parent::initialize();

    $this->loadComponent('Paginator');
    $this->Authentication->allowUnauthenticated(['getList', 'index', 'view', 'search']);
  }

  public function view($slug = null)
  {
    if (is_numeric($slug)) {
      $article = $this->Articles->findById($slug)
        ->contain(['Tags', 'Destinations'])
        ->firstOrFail();
    } else {
      $article = $this->Articles->findBySlug($slug)
        ->contain(['Tags', 'Destinations'])
        ->firstOrFail();
    }
    $lastModified = $article->modified->getTimestamp();
    $etag = md5($lastModified . '-' . $article->id);

    header("ETag: \"$etag\"");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s", $lastModified) . " GMT");

    // Verifica ETag del client
    if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && @trim($_SERVER['HTTP_IF_NONE_MATCH']) === "\"$etag\"") {
      header("HTTP/1.1 304 Not Modified");
      exit;
    }

    $this->set(compact('article'));
    $this->set('user', $this->request->getAttribute('identity'));
    $this->viewBuilder()->setOption('serialize', ['article']);
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

  /**
   * Index method.
   *
   * @return \Cake\Http\Response|void|null Renders view
   */
  public function index()
  {
    $query = $this->Articles->find()
      ->contain(['Tags'])
      ->where(['Articles.published' => 1])
      ->order(['Articles.modified DESC']);

    $promoted = $this->request->getQuery('promoted');
    if ($promoted) {
      $query->order(['promoted DESC']);
    }
    //Valori ammessi: [0,1,*]
    $archived = $this->request->getQuery('archived');
    if ($archived == 1) {
      $query->where(['archived' => 1]);
    } elseif ($archived == "0") {
      $query->where(['OR' => ['archived' => 0, 'archived is' => null]]);
    }

    $slider = $this->request->getQuery('slider');
    if ($slider) {
      $query->order(['slider DESC']);
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

    $tag = $this->request->getQuery('tag');
    if ($tag) {
      $query->contain(['Tags' => ['fields' => ['id', 'title']]]);
      $query->innerJoinWith('Tags', function ($q) use ($tag) {
        return $q->where(['Tags.id IN' => $tag]);
      });
    }


    $destinations = [];
    $destination_id = $this->request->getQuery('destination_id');
    //dd($destination_id);
    if ($destination_id) {
      //dd($destination_id);
      $destinations = $this->Articles->Destinations->find()
        ->select(['name', 'slug'])
        ->where(['Destinations.id IN' => $destination_id])
        ->toList();

      if (is_array($destination_id)) {
        $query->where(['Articles.destination_id IN' => $destination_id]);
      } else {
        $query->where(['Articles.destination_id' => $destination_id]);
      }
    }
    $query->contain(['Destinations' => ['fields' => ['id', 'name', 'slug']]]);


    $articles = $this->paginate($query);
    $pagination = $this->Paginator->getPagingParams();
    $this->set(compact('articles', 'pagination', 'destinations'));
    $this->viewBuilder()->setOption('serialize', ['articles', 'pagination', 'destinations']);
  }

  public function search()
  {
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
      'anno' => 'YEAR(Articles.modified)',
    ])
      ->group(['YEAR(Articles.modified)', 'MONTH(Articles.modified)'])
      ->order(['YEAR(Articles.modified) DESC', 'MONTH(Articles.modified) DESC'])
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
