<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\StaticModel;
use Cake\Core\Configure;
use Cake\Http\Exception\NotFoundException;
use Cake\I18n\I18n;
use PSpell\Config;

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
	private $staticFilesPath;

  //Necessario per gestire la risposta in json della view

	public function initialize(): void {
		parent::initialize();
		$this->loadComponent('RequestHandler');
	  	$this->Authentication->allowUnauthenticated(['index','view','get']);

	  //Imposto la cartella dove si trovano le immagini statiche
		$sitedir = Configure::read('sitedir');
		$this->staticImgPath = "/$sitedir/static/img/";
		$this->set('staticImgPath', $this->staticImgPath);
		$this->StaticModel = new StaticModel();
		$this->staticFilesPath = $sitedir . DS . 'static' . DS ;
	}

	/**
	 * Index method
	 *
	 * @return \Cake\Http\Response|void
	 */
	public function index(...$path) {		
		$name = $this->staticFilesPath . $this->StaticModel->combina_path($path);

		$limit = $this->request->getQuery('limit');
		if (empty($limit)) {
			$limit = 100;
		}

		$risult = $this->StaticModel->find($name, $limit);

		$this->set('files', $risult);
		$this->viewBuilder()->setOption('serialize', ['files']);

		$languages = Configure::read('I18n.languages');
			//Se il primo elemento è la lingua lo butto
			if (in_array($path[0], $languages)) {
				I18n::setLocale($path[0]);
				array_shift($path);
			}

		//Se la pagina è di tipo blog, uso un template specifico
		if ($path[0] == 'blog' || (isset($path[1]) && $path[1] == 'blog')) {
			$this->render('index/blog');
		}

	  //Se la pagina è di tipo blog, uso un template specifico
		if ($path[0] == 'portfolio' || (isset($path[1]) && $path[1] == 'portfolio')) {
			$this->render('index/portfolio');
		}
	}

	public function view(...$path) {
		$sitedir = Configure::read('sitedir');
		$name = $this->StaticModel->combina_path($path);
		$page = $subpage = null;

		if (!empty($path[0])) {
			$page = $path[0];
		}
		if (!empty($path[1])) {
			$subpage = $path[1];
		}
		$keywords = Configure::read('keywords');
		$this->set(compact('page', 'subpage','keywords'));

	  //verifico che il file esista
		$fname = $sitedir . DS . 'static' . DS . $name . '.md';
		if (!file_exists($fname)) {
			throw new NotFoundException();
		}

	  //Ciclo su tutte le variabili e le passo alla view
		$dati = $this->StaticModel->leggi_file_md($fname);
		$k = array_keys($dati);
		$vs = [];
		foreach ($k as $variabile) {
			$this->set($variabile, $dati[$variabile]);
			$vs[] = $variabile;
		}
		
	   $this->viewBuilder()->setOption('serialize', $vs);

	  //Se il path[0] contiene una slash devo fare una separazione in pezzi
	  //Massimoi - 2020-01-20 Problema introdotto con la gestione particolare dei path
		$languages = Configure::read('I18n.languages');
		if (strpos($path[0], '/')) {
			$path = explode('/', $path[0]);
		  //Se il primo elemento è la lingua lo butto
			if (in_array($path[0], $languages)) {				
				I18n::setLocale($path[0]);
				array_shift($path);
			}
		}

	  //Se la pagina è di tipo specifico, uso un template specifico
		$special_template = Configure::read('specialTemplate');
		if (in_array($path[0], $special_template)) {
			$this->render($path[0]);
		}
	}

	//Restituisce l'articolo specifico per il blog il cui nome è contenuto nella variabile $param 
	//del di configurazione che si trova in static
	//In questo modo gli utenti posso cambiare facilmente il nome dell'articolo toccando il file config.ini
	public function get($param){
		//Apro il file di configurazione
		$config = parse_ini_file($this->staticFilesPath . 'config.ini');
		//Cerco la variabile che si chiama $param
		if (isset($config[$param])){
			$fname  = $config[$param];
		} else { //Se la variabile non c'è prendo il primo file che trovo
			$fname = $this->StaticModel->find("blog", 1);
		}		
		//Altrimenti chiamo la funzione view con il nome del file contenuto nella variabile
		$this->view($fname);
	}

	

}
