<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Model\StaticModel;

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
    $this->set('static',$static);
  }
}