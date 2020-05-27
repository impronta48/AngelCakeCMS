<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Model\StaticModel;
use Cake\ORM\TableRegistry;

/**
 * SiteMaps Controller
 *
 */
class SitemapsController extends AppController
{
  //Necessario per gestire la risposta in json della view
  public function initialize(): void
  {
	parent::initialize();
	$this->modelClass = false;
	$this->loadComponent('RequestHandler');
  //$this->Authentication->allowUnauthenticated(['index']);
  }

  public function index(){
    $s = new StaticModel();
    $static=$s->findAll();

    $articles = TableRegistry::getTableLocator()->get('Articles')->find()
              ->select(['id','slug','modified'])
              ->where(['published' => true])
              ->order(['modified'=> 'desc'])
              ->all();                      
                 
    $this->set(compact('static','articles'));
  }
}