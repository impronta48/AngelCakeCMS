<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\Utility\Text;
use Cake\Http\Exception\NotFoundException;
use Cake\I18n\I18n;

/**
 * Destinations Controller
 *
 * @property \App\Model\Table\DestinationsTable $Destinations
 *
 * @method \App\Model\Entity\Destination[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class DestinationsController extends AppController
{

  public function initialize(): void
  {
    parent::initialize();
    $this->Authentication->allowUnauthenticated(['prenota', 'index', 'experience', 'activities', 'tours', 'addioNubilato', 'rent', 'view', 'count', 'prezzi']);
  }

  public $paginate = [
    'limit' => 60,
  ];

  public function destination_in_session($destination = null)
  {
    return;
    if ($destination) {
      $this->Cookie->write('Destination.id', $destination->id, false, '7 days');
    } else {
      $this->Cookie->delete('Destination');
    }
  }

  private function get_seo_destination_name($nomeseo = null)
  {
    $only = $this->request->getQuery('only');
    if (!empty($only)) {
      $only = explode(",", $only);
    }

    if (is_numeric($nomeseo)) {
      $destination = $this->Destinations->findByIdAndPublished($nomeseo, 1);
      if ($only) {
        $destination->select($only);
      }

      $destination = $destination->first();
    } else {
      $destination = $this->Destinations->findBySlugAndPublished($nomeseo, 1);
      if ($only) {
        $destination->select($only);
      }

      $destination = $destination->first();
    }

    if (empty($destination)) {
      //TODO: Attenzione se il nomeseo contiene un apostrofo non funziona (150 errori!)
      $nomeseo_slug = Text::slug($nomeseo);
      $nomeseo = str_replace('-', ' ', $nomeseo);
      $destination = $this->Destinations->find()
        ->where([
          'nomiseo LIKE' => "%$nomeseo%",
          'published' => 1
        ])
        ->first();
    } else {
      $nomeseo = $destination->preposition . ' ' . $destination->name;
      $nomeseo_slug = $destination->slug;
    }

    if (empty($destination)) {
      throw new NotFoundException(__('Invalid Destination'));
    }

    //Creo un array dai nomiseo
    if (isset($destination->nomiseo) && !empty($destination->nomiseo)) {
      $nomiseo = explode(',', $destination->nomiseo);
      //Cerco l'elemento che contiene il mio nomeseo
      foreach ($nomiseo as $n) {
        $n = trim($n);
        if (stripos($n, $nomeseo) !== false) {
          $nomeseo = $n;
          break;
        }
      }
    } else {
      $nomeseo = $destination->preposition . ' ' . $destination->name;
    }

    //Forzo e-tag in modo da cancellare la cache
    $lastModified = $destination->modified ? $destination->modified->getTimestamp() : time();
    $locale = I18n::getLocale();
    $etag = md5($lastModified . '-' . $destination->id . '-' . $locale);

    header("ETag: \"$etag\"");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s", $lastModified) . " GMT");
    $this->response = $this->response->withHeader('Vary', 'Accept-Language');

    // Verifica ETag del client
    if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && @trim($_SERVER['HTTP_IF_NONE_MATCH']) === "\"$etag\"") {
      header("HTTP/1.1 304 Not Modified");
      exit;
    }

    $this->set('nomeseo', $nomeseo);
    $this->set('canonical',  $nomeseo_slug);
    $this->set('destination', $destination);

    //Salvo la destination in session
    $this->destination_in_session($destination);

    return $destination;
  }

  /**
   * Index method
   *
   * @return \Cake\Http\Response|void
   */
  public function index()
  {
    $existing_columns = $this->Destinations->getSchema()->columns();
    $desired_columns = [
      'id',
      'name',
      'slug',
      'preposition',
      'nazione_id',
      'regione',
      'lat',
      'lon',
      'description',
      'nomiseo',
      'published',
      'published',
      'created',
      'modified',
      'chiuso',
      'level'
    ];
    $select_columns = array_intersect($existing_columns, $desired_columns);
    $order_columns = array_intersect($existing_columns, ['nazione_id', 'name']);

    $query = $this->Destinations->find()
      ->where(['published' => true]);

    $specific_id = $this->request->getQuery('id');
    if (!empty($specific_id)) {
      $query->where(['id' => $specific_id]);
    }

    $random = $this->request->getQuery('random');
    if (!empty($random)) {
      $query->order('rand()');
    } else {
      $query->order($order_columns);
    }

    $only = $this->request->getQuery('only');
    if (!empty($only)) {
      $columns = explode(',', $only);
      if (array_search('payment_conf', $columns) !== false) {
        unset($columns[array_search('payment_conf', $columns)]);
      }
      if (array_search('caparra', $columns) !== false) {
        unset($columns[array_search('caparra', $columns)]);
      }
      if (empty($columns)) {
        $query->select($select_columns);
      } else {
        $query->select($columns);
      }
    } else {
      $query->select($select_columns)
        ->contain(['Articles']);
    }

    $limit = $this->request->getQuery('limit');
    if (!empty($limit)) {
      $query->limit($limit);
    }

    $prezzi = $this->request->getQuery('prezzi');
    $this->set(compact('prezzi'));

    $count = $this->request->getQuery('count');
    if (!empty($count)) {
      $count = $query->count();
      $this->set('count', $count);
      $this->viewBuilder()->setOption('serialize', 'count');
    } else {
      $locale = I18n::getLocale();
      $ckey = $locale . '-destinations-' . md5(serialize($this->request->getQuery()));
      if (!$this->request->is('json')) {
        $destinations = $this->paginate($query->cache($ckey));
      } else {
        $destinations = $query->cache($ckey);
      }

      $this->set(compact('destinations'));
      $this->viewBuilder()->setOption('serialize', 'destinations');
    }
  }

  public function experience($nomeseo = null)
  {
    $destination = $this->get_seo_destination_name($nomeseo);
    $this->loadModel('Cyclomap.Percorsi');
    $percorsi = $this->Percorsi->find('all')
      ->where([
        'destination_id' => $destination->id,
        'published' => 1,
        'tipo_id' => 6,
      ])
      ->limit(10)
      ->toArray();
    $this->set('percorsi', $percorsi);
  }

  public function activities($nomeseo = null)
  {
    $destination = $this->get_seo_destination_name($nomeseo);
    $this->loadModel('Cyclomap.Percorsi');
    $percorsi = $this->Percorsi->find('all')
      ->where([
        'destination_id' => $destination->id,
        'published' => 1,
        'tipo_id' => 2,
      ])
      ->limit(10)
      ->toArray();
    $this->set('percorsi', $percorsi);
  }

  public function tours($nomeseo = null)
  {
    $destination = $this->get_seo_destination_name($nomeseo);
    $this->loadModel('Cyclomap.Percorsi');
    $percorsi = $this->Percorsi->find('all')
      ->where([
        'destination_id' => $destination->id,
        'published' => 1,
        'tipo_id' => 1,
      ])
      ->limit(10)
      ->toArray();
    $this->set('percorsi', $percorsi);
  }

  public function addioNubilato($nomeseo = null)
  {
    $this->get_seo_destination_name($nomeseo);
  }

  public function rent($nomeseo = null)
  {
    $this->get_seo_destination_name($nomeseo);
    $this->render('Ebike2021.view');
  }

  public function view($nomeseo = null)
  {
    $this->get_seo_destination_name($nomeseo);
    $this->viewBuilder()->setOption('serialize', 'destination');
  }

  public function count()
  {
    $this->Destinations->recursive = -1;
    $c = $this->Destinations->find('count', ['conditions' => ['published' => 1]]);

    //Se mi hanno chiamato da RequestAction (di solito da un element)
    if ($this->request->is('requested')) {
      return $c;
    }
    $this->set('count', $c);
  }

  public function prezzi($nomeseo = null)
  {
    if (empty($nomeseo)) {
      $nomeseo = $this->request->getQuery('destination');
    }
    if (empty($nomeseo)) {
      return $this->redirect(['controller' => 'Destinations', 'action' => 'index', '?' => ['prezzi' => true]]);
    }
    $destination = $this->get_seo_destination_name($nomeseo);
    $this->set(compact('destination'));

    // tipi di bici disponibili
    $this->loadModel('Cyclomap.Tipibici');
    $tipibici = $this->Tipibici->find(
      'all',
      [
        'recursive' => -1,
        'conditions' => [
          'destination_id' => $destination->id,
          'published' => 1,
        ],
        'order' => 'tariffa_intera ASC'
      ]
    )->toArray();
    $this->set('tipibici', $tipibici);

    // addons disponibili
    $this->loadModel('Cyclomap.Addon');
    $addon_list = $this->Addon->find('all', [
      'recursive' => -1,
      'conditions' => [
        'destination_id' => $destination->id,
        'published' => 1,
      ],
      'order' => ['name'],
    ])->toArray();
    $this->set('addons', $addon_list);

    /*  $this->loadModel('Cyclomap.Percorsi');
    $esperienze = $this->Percorsi->find()
      ->where([
        'destination_id' => $destination->id,
        'published' => 1,
        'tipo_id' => 6,
      ])->toArray();
    $this->set('esperienze', $esperienze);

    $percorsi = $this->Percorsi->find()
      ->where([
        'destination_id' => $destination->id,
        'published' => 1,
        'tipo_id' => 2,
      ])->toArray();
    $this->set('percorsi', $percorsi); */

    $destinations = $this->Destinations->find('all')->toArray();
    $this->set('destinations', $destinations);
  }

  public function prenota($destination = null)
  {
    $existing_columns = $this->Destinations->getSchema()->columns();
    $desired_columns = [
      'id',
      'name',
      'slug',
      'nazione_id',
      'regione',
      'published',
    ];
    $destinations = $this->Destinations->find()
      ->select($desired_columns)
      ->order(['name'])
      ->where(['published' => true]);
    if ($destination) {
      $destinations->where(['slug' => $destination]);
    }

    $this->set('destinations', $destinations);
  }

  /**
   * usato via request action in alcune viste per settare in sessione la destinazione indicata
   */
  public function in_session($destination)
  {
    if (is_numeric($destination)) {
      $destination = $this->Destinations->findById($destination);
    } else {
      $destination = $this->Destinations->findBySlug($destination);
    }
    $this->destination_in_session($destination);

    return true; // suppongo che venga sempre invocato via request action
  }
}
