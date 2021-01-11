<?php

namespace App\Controller;

use App\Controller\AppController;
use App\Model\Entity\Destination;
use Cake\Cache\Cache;
use Cake\Core\App;

use function Psy\debug;
use Cake\Routing\Router;
use Cake\Utility\Inflector;
use Cake\View\Exception\MissingTemplateException;
use Exception;
use Psr\Log\LogLevel;

/**
 * Destinations Controller
 *
 * @property \App\Model\Table\DestinationsTable $Destinations
 *
 * @method \App\Model\Entity\Destination[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class DestinationsController extends AppController
{
  public $paginate = [
    'limit' => 12,
  ];

  /**
   * Index method
   *
   * @return \Cake\Http\Response|void
   */
  public function index()
  {
    $destinations = $this->paginate($this->Destinations, [
      'contain' => ['Articles'],
      'conditions' => ['show_in_list' => TRUE],
      'order' => ['chiuso ASC', 'name'],
      'limit' => 100,
    ]);
    $this->set(compact('destinations'));
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
        $slug = $id;
        $id = $this->Destinations->findBySlug($id)->firstOrFail()->id;
      } catch (\Cake\Datasource\Exception\RecordNotFoundException $ex) {
        $this->log(sprintf('Record not found in database (id = %d)!', $id), LogLevel::WARNING);
      }
    }

    $query->where(['id' => $id]);                   //Filtro le destination
    $destination = $query->first();
    $this->set('destination', $destination);

    $articles_q->where(['destination_id' => $id]);  //Filtro gli articoli
    $articles_q->where(['published' => true]);      //Mostro solo quelli pubblicati
    $art = $this->paginate($articles_q);
    $this->set('articles', $art);
    $this->set('archived', $a);
    try {
      $this->render($slug);
    } catch (MissingTemplateException $e) {
      $this->viewBuilder()->setTemplate(null);
      $this->render(null);
    }
  }
}
