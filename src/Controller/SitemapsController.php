<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\StaticModel;
use Cake\ORM\TableRegistry;

/**
 * SiteMaps Controller
 *
 */
class SitemapsController extends AppController
{
  //Necessario per gestire la risposta in json della view

	public function initialize(): void {
		parent::initialize();
		$this->modelClass = false;
		$this->loadComponent('RequestHandler');
		$this->Authentication->allowUnauthenticated(['index']);
	}

	public function index() {
        $this->RequestHandler->renderAs($this, 'xml');

		$s = new StaticModel();
 		$static = $s->findAll();

		$articles = TableRegistry::getTableLocator()->get('Articles')->find()
			  ->select(['id','slug','modified'])
			  ->where(['published' => true])
			  ->order(['modified' => 'desc'])
			  ->all();

		$destinations = TableRegistry::getTableLocator()->get('Destinations')->find()
			  ->select(['id', 'name', 'slug'])
			  ->where(['published' => true])
			  ->order(['name' => 'asc'])
			  ->all();

		// $this->viewBuilder()->disableAutoLayout();
		$this->set(compact('static', 'articles', 'destinations'));
	}
}
