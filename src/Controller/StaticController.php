<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Http\Exception\NotFoundException;
use App\Model\StaticModel;
use Cake\Filesystem\Folder;
use Cake\Core\Configure;


/**
 * Static Controller
 *
 * @property \App\Model\Table\StaticTable $Static
 *
 * @method \App\Model\Entity\Participant[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class StaticController extends AppController
{
  private $staticImgPath;
  private $StaticModel;

  //Necessario per gestire la risposta in json della view
  public function initialize(): void
  {
    parent::initialize();
    $this->modelClass = false;
    $this->loadComponent('RequestHandler');
    //$this->Authentication->allowUnauthenticated(['index','view']);

    //Imposto la cartella dove si trovano le immagini statiche
    $sitedir = Configure::read('sitedir');
    $this->staticImgPath = "/$sitedir/static/img/";
    $this->set('staticImgPath', $this->staticImgPath);
    $this->StaticModel = new StaticModel();
  }

  /**
   * Index method
   *
   * @return \Cake\Http\Response|void
   */
  public function index(...$path)
  {
    $sitedir = Configure::read('sitedir');
    $name = $sitedir . DS . 'static' . DS . $this->StaticModel->combina_path(...$path);
    $page = $subpage = null;

    if (!empty($path[0])) {
      $page = $path[0];
    }
    if (!empty($path[1])) {
      $subpage = $path[1];
    }

    //Caricare il nostro frontmatter in modo che legga tutti i file nella cartella static
    $dir = new Folder($name);
    $files = $dir->find('.*\.md');

    //fai un foreach sui file e per ogni file chiami la leggi_file_md
    //ti metti il risultato in qualche variabile e lo passi alla view
    //Prendi solo i primi 5
    //dd($files);
    $risult = [];
    foreach ($files as $k => $f) {
      //Se inizia con _ ignoro
      if ($f[0] != '_') {
        $risult[$k]['file']  = $f;
        $risult[$k]['dati'] = $this->StaticModel->leggi_file_md($name . DS . $f);
        if (!isset($risult[$k]['dati']['date'])) {
          $risult[$k]['dati']['date'] = null;
        }
      }
    }

    //Ordino l'array dei risultati per il campo date invertito
    usort($risult, function ($a, $b) {
      return -1 * strcmp($a['dati']['date'], $b['dati']['date']);
    });

    $this->set('files', $risult);
    $this->set('_serialize', 'files');

    //Se la pagina è di tipo blog, uso un template specifico
    if ($path[0] == 'blog' || (isset($path[1]) && $path[1] == 'blog')) {
      $this->render('index/blog');
    }

    //Se la pagina è di tipo blog, uso un template specifico
    if ($path[0] == 'portfolio' || (isset($path[1]) && $path[1] == 'portfolio')) {
      $this->render('index/portfolio');
    }
  }

  public function view(...$path)
  {
    $sitedir = Configure::read('sitedir');
    $name = $this->StaticModel->combina_path(...$path);
    $page = $subpage = null;


    if (!empty($path[0])) {
      $page = $path[0];
    }
    if (!empty($path[1])) {
      $subpage = $path[1];
    }
    $this->set(compact('page', 'subpage'));

    //verifico che il file esista
    $fname = $sitedir . DS . 'static' . DS . $name . '.md';
    if (!file_exists($fname)) {
      throw new NotFoundException();
    }

    //Ciclo su tutte le variabili e le passo alla view
    $dati = $this->StaticModel->leggi_file_md($fname);
    $k = array_keys($dati);
    foreach ($k as $variabile) {
      $this->set($variabile, $dati[$variabile]);
    }

    //Se il path[0] contiene una slash devo fare una separazione in pezzi
    //Massimoi - 2020-01-20 Problema introdotto con la gestione particolare dei path
    $languages = Configure::read('App.Languages');
    if (strpos($path[0], '/')) {
      $path = explode('/', $path[0]);
      //Se il primo elemento è la lingua lo butto
      if (in_array($path[0], $languages)) {
        array_shift($path);
      }
    }

    //Se la pagina è di tipo specifico, uso un template specifico
    $special_template = Configure::read('specialTemplate');
    if (in_array($path[0], $special_template)) {
      $this->render($path[0]);
    }
  }
}
