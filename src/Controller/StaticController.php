<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\StaticModel;
use Cake\Core\Configure;
use Cake\Http\Exception\NotFoundException;
use Cake\I18n\I18n;
use DOMDocument;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

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
		$this->modelClass = false;
		$this->loadComponent('RequestHandler');
	  	$this->Authentication->allowUnauthenticated(['index','view','get', 'getWebdav']);

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
		$this->set(compact('page', 'subpage'));

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

	public function getWebdav(){
		$risultato = null;

		if ($this->request->is('post')) {
		
		// Set the webdav server details
		$webdav_server = Configure::read('Webdav.server');
		$webdav_username = Configure::read('Webdav.username');
		$webdav_password = Configure::read('Webdav.password');

		// La cartella da cui leggere è 
		// https://cloud.mobilitysquare.eu/remote.php/dav/files/sito.bikesquare/
		// Set the directory path to download files from
		$webdav_directory = Configure::read('Webdav.remoteFolder');
		/* * Attenzione: assicurarsi che l'indirizzo del server sia indicato come
           * https://{server}/remote.php/dav/files/{user}/
           * e non solo come https://server/
		*/
		$webdav_server = "$webdav_server/remote.php/dav/files/$webdav_username";
		$local_folder = WWW_ROOT . $this->staticFilesPath;

		// Create a new Guzzle client
		$client = new Client([
			'base_uri' => $webdav_server,
			'auth' => [$webdav_username, $webdav_password],
		]);

		$risultato = "Connessione al server $webdav_server: OK\n";

		// Send a PROPFIND request to the server to get the list of files
		$response = $client->request('PROPFIND', $webdav_server . $webdav_directory, [
			RequestOptions::BODY => '<?xml version="1.0" encoding="UTF-8" ?><propfind xmlns="DAV:"><prop><getlastmodified/></prop></propfind>',
			RequestOptions::HEADERS => [
				'Content-Type' => 'application/xml',
			],
		]);

		// Parse the XML response
		$res = (string) $response->getBody();		
		// Parse the XML response using DOMDocument
		$dom = new DOMDocument();
		$dom->loadXML($res);
		
		// Loop through each file and directory and download files if they're newer than the local files
		foreach ($dom->getElementsByTagNameNS('DAV:', 'response') as $item) {
			$href = $item->getElementsByTagNameNS('DAV:', 'href')->item(0)->nodeValue;
			
			// Check if the item is a file or directory
			if (substr($href, -1) !== '/') {
				// The item is a file

				$filename = basename($href);
				$file_url = Configure::read('Webdav.server') . $href;
				$filename = $local_folder . basename($href);

				// Get the last modified time of the remote file
				$last_modified_remote = strtotime($item->getElementsByTagNameNS('DAV:', 'getlastmodified')->item(0)->nodeValue);

				// Check if the local file exists and get its last modified time
				if (file_exists($filename)) {
					$last_modified_local = filemtime($filename);
				} else {
					$last_modified_local = 0; // Set to 0 if the file doesn't exist locally
				}

				// Download the file if it's newer than the local file
				if ($last_modified_remote > $last_modified_local) {
					// Download the file using Guzzle
					$response = $client->request('GET', $file_url);

					// Save the downloaded file to disk
					file_put_contents($filename, $response->getBody());

					$risultato .= "Downloaded updated file: " . $filename . "\n";
				} else {
					$risultato .= "Skipping file: " . $filename . "\n";
				}
			} else {
				// The item is a directory
				// Get the directory path relative to the webdav root directory				
				// the path i get from webdav is like this
				// /remote.php/dav/files/sito.bikesquare/sito-b2b/_drafts/
				// i need to remove the first part
				// /remote.php/dav/files/sito.bikesquare/sito-b2b/
				// which is "/remote.php/dav/files/$user/$webdav_directory/" 
				$relative_dir_path = str_replace("/remote.php/dav/files/{$webdav_username}{$webdav_directory}", "", $href);		

				// Recursively create the directory on disk if it doesn't exist
				if (!file_exists("{$local_folder}{$relative_dir_path}")) {
					mkdir("{$local_folder}{$relative_dir_path}", 0777, true);
					$risultato .= "Created directory: " . $relative_dir_path . "\n";
				} else {
					$risultato .= "Skipping directory: " . $relative_dir_path . "\n";
				}
			}
			}	 //if is post
		}		
		$this->set('risultato', $risultato);
	}

}
