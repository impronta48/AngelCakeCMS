<?php

namespace App\Controller\Admin;

use App\Controller\AppController;
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
      $risult[$k]['file']  = $f;
      $risult[$k]['dati'] = $this->StaticModel->leggi_file_md($name . DS . $f);
      if (!isset($risult[$k]['dati']['date'])) {
        $risult[$k]['dati']['date'] = null;
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



  //Legge una cartella remota di webDav e aggiorna la cartella static del sito corrente
  public function getRemote()
  {
    $sitedir = Configure::read('sitedir');
    $localFolder = WWW_ROOT . $sitedir . DS . 'static';
    $remoteFolder = Configure::read('rclone.staticFolder');
    $config = Configure::read('rclone.config');
    $cmd = "rclone --config=$config copy $remoteFolder $localFolder -v 2>&1";

    if ($this->request->is('post')) {
      //shell_exec
      if (function_exists('exec')) {
        $output = 'Sincronizzazione eseguita<br>';
        //debug(exec('pwd'));
        var_dump(exec($cmd, $output));
        //echo $return;
        //var_dump($output);    
      } else {
        $output = 'Command execution not possible on this system';
      }
      $this->set('msg', $output);
    }
    $this->set('msg', 'Importazione NextCloud.');
  }
}
