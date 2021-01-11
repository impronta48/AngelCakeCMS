<?php

namespace App\Controller\Admin;

use App\Controller\AppController;
use App\Model\StaticModel;
use Cake\Filesystem\Folder;
use Cake\Core\Configure;
use Cake\Filesystem\File;
use Cake\Routing\Router;

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
    $files = $dir->read(true);
    $this->set('files', $files);
    $this->set('path', $path);
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

  public function edit(...$fname)
  {
    $sitedir = Configure::read('sitedir');
    $absoluteFname = WWW_ROOT . $sitedir . DS . 'static/' . $this->StaticModel->combina_path(...$fname);
    $file = new File($absoluteFname);
    $static = $file->read();
    $title = $this->StaticModel->combina_path(...$fname);
    //$static = $this->StaticModel->leggi_file_md($absoluteFname);
    $this->set('path', $fname);
    $this->set(compact(['static', 'title']));
  }
}
