<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;
use App\Model\StaticModel;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Filesystem\Folder;
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
	private $staticFilesPath;
	private $StaticModel;
	private $client;		//The WebDav Client object (Guzzle))
	private $webdav_server;
	private $webdav_username;
	private $webdav_directory;
	private $local_folder;

	public function initialize(): void
	{
		parent::initialize();
		$this->loadComponent('RequestHandler');

		//Imposto la cartella dove si trovano le immagini statiche
		$sitedir = Configure::read('sitedir');
		$this->staticImgPath = "/$sitedir/static/img/";
		$this->set('staticImgPath', $this->staticImgPath);
		$this->StaticModel = new StaticModel();
		$this->staticFilesPath = $sitedir . DS . 'static' . DS;
	}

	/**
	 * Index method
	 *
	 * @return \Cake\Http\Response|void
	 */
	public function index(...$path)
	{
		$sitedir = Configure::read('sitedir');
		$name = $sitedir . DS . 'static' . DS . $this->StaticModel->combina_path($path);

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
		$this->Authorization->skipAuthorization();
	}

	private function openWebdav()
	{
		// Set the webdav server details
		$this->webdav_server = Configure::read('Webdav.server');
		$this->webdav_username = Configure::read('Webdav.username');
		$webdav_password = Configure::read('Webdav.password');

		// La cartella da cui leggere è 
		// https://cloud.mobilitysquare.eu/remote.php/dav/files/sito.bikesquare/
		// Set the directory path to download files from
		$this->webdav_directory = Configure::read('Webdav.remoteFolder');
		/* * Attenzione: assicurarsi che l'indirizzo del server sia indicato come
           * https://{server}/remote.php/dav/files/{user}/
           * e non solo come https://server/
		*/
		$this->webdav_server = "$this->webdav_server/remote.php/dav/files/$this->webdav_username";
		$this->local_folder = WWW_ROOT . $this->staticFilesPath;

		// Create a new Guzzle client
		$this->client = new Client([
			'base_uri' => $this->webdav_server,
			'auth' => [$this->webdav_username, $webdav_password],
		]);
	}

	public function getWebdav()
	{
		$risultato = null;
		$this->Authorization->skipAuthorization();
		$this->openWebdav();

		$data = $this->request->input('json_decode',true);

		$filename = $data['filename'];
		$file_url = $data['file_url'];
		$last_modified_remote = $data['last_modified_remote'];

		// Download the file using Guzzle
		$response = $this->client->request('GET', $file_url);

		// Save the downloaded file to disk
		file_put_contents($filename, $response->getBody());

		//Set the $last_modified_remote as the file modification time using touch
		touch($filename, $last_modified_remote);

		Cache::clear();
		$risultato .= "Downloaded updated file: " . $filename . "\n";
		$this->log("Downloaded updated file: " . $filename, 'debug');
		$this->set('risultato', $risultato);
		$this->set('_serialize', 'risultato');
	}

	//Restituisce una lista di file e cartelle in formato JSON per sync i dati
	//Origin deve essere relativo alla root sia per il locale che per il remoto
	//quindi se ho blog --> webdav/sito-b2b/blog 
	//se ho eng/blog --> webdav/sito-b2b/eng/blog
	public function listWebdav()
	{		
		$this->Authorization->skipAuthorization();
		$risultato = [];
		
		if ($this->request->is(['json']) && $this->request->is(['post'])) {

			//Leggo il valore del post
			$data = $this->request->input('json_decode', true);
			$origin = $data['origin'] ?? '';

			$this->openWebdav();
			$this->local_folder = $this->local_folder . $origin;

			// Send a PROPFIND request to the server to get the list of files
			$response = $this->client->request('PROPFIND', $this->webdav_server . $this->webdav_directory . $origin, [
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

			$risultato[]['descr'] = "File list retrieved: OK";

			// Loop through each file and directory and download files if they're newer than the local files
			foreach ($dom->getElementsByTagNameNS('DAV:', 'response') as $item) {
				$href = $item->getElementsByTagNameNS('DAV:', 'href')->item(0)->nodeValue;

				// Get the last modified time of the remote file
				$last_modified_remote = strtotime($item->getElementsByTagNameNS('DAV:', 'getlastmodified')->item(0)->nodeValue);

				// Check if the item is a file or directory
				if (substr($href, -1) !== '/') {
					// The item is a file

					$filename = basename($href);
					$file_url = Configure::read('Webdav.server') . $href;
					$filename = $this->local_folder  . basename($href);

					// Check if the local file exists and get its last modified time
					if (file_exists($filename)) {
						$last_modified_local = filemtime($filename);
					} else {
						$last_modified_local = 0; // Set to 0 if the file doesn't exist locally
					}

					// Download the file if it's newer than the local file
					if ($last_modified_remote > $last_modified_local) {

						$risultato[] = [
							'action' => 'get',
							'descr' => "File to be downloaded: $filename",
							'filename' => $filename,
							'file_url' => $file_url,
							'last_modified_remote' => $last_modified_remote,
							'last_modified_local' => $last_modified_local,
						];
					} else {
						$risultato[] = [
							'action' => 'skip',
							'descr' => "File ignored: $filename",
							'filename' => $filename,
							'file_url' => $file_url,
							'last_modified_remote' => $last_modified_remote,
							'last_modified_local' => $last_modified_local,
						];
					}
				} else {
					// The item is a directory
					// Get the directory path relative to the webdav root directory				
					// the path i get from webdav is like this
					// /remote.php/dav/files/sito.bikesquare/sito-b2b/_drafts/
					// i need to remove the first part
					// /remote.php/dav/files/sito.bikesquare/sito-b2b/
					// which is "/remote.php/dav/files/$user/$webdav_directory/" 
					$relative_dir_path = str_replace("/remote.php/dav/files/{$this->webdav_username}{$this->webdav_directory}{$origin}", "", $href);

					// Recursively create the directory on disk if it doesn't exist
					if (!file_exists("{$this->local_folder}{$relative_dir_path}")) {
						mkdir("{$this->local_folder}{$relative_dir_path}", 0777, true);
						$risultato[]['descr'] =  "Created directory: " . $relative_dir_path . "\n";
						$this->log("Created directory: " . $relative_dir_path, 'debug');
						//Set the $last_modified_remote as the file modification time using touch
						touch("{$this->local_folder}{$relative_dir_path}", $last_modified_remote);
						Cache::clear();
					} else {
						$risultato[]['descr'] =  "Skipping directory: " . $relative_dir_path . "\n";
						$this->log("Skipping directory: " . $relative_dir_path, 'debug');
					}

					if ($relative_dir_path) {						
						$relative_dir_path = str_replace("/remote.php/dav/files/{$this->webdav_username}{$this->webdav_directory}", "", $href);
						$this->log("Going Recursive: " . $relative_dir_path, 'debug');
						//Nella risposta di webdav trovo la cartella stessa, devo ignorarla
						if ($relative_dir_path != $origin . "/" ) {
							// $this->listWebdav($relative_dir_path); // non chiamo subito la ricorsione perchè è troppo lento faccio fare al chiamante
							$risultato[] = [
								'action' => 'list',
								'file_url' => $relative_dir_path,
								'descr' => "Going Recursive: $relative_dir_path",
								'recursive' => $relative_dir_path,
							];
						}
					}
				}
			}	 //if is json
		}
		$this->set('risultato', $risultato);
		$this->set('_serialize', 'risultato');
	}


	public function edit(...$fname)
	{
		$this->Authorization->skipAuthorization();
		$sitedir = Configure::read('sitedir');
		$fnameStr = $this->StaticModel->combina_path($fname);
		$absoluteFname = WWW_ROOT . $sitedir . DS . 'static/' . $fnameStr;
		array_pop($fname);

		//Salvo
		if ($this->request->is(['post', 'put'])) {
			$static = $this->request->getData('static');
			if ($this->StaticModel->save($absoluteFname, $static)) {
				$this->Flash->success('Pagina statica salvata con successo');

				return $this->redirect(array_merge(['action' => 'index'], $fname));
			}

			return $this->Flash->error('Errore durante il salvataggio della pagina statica: ' . $fnameStr);
		}

		//Leggo
		$title = $this->StaticModel->combina_path($fname);
		$static = $this->StaticModel->get($absoluteFname);

		//Non devo chiamare leggi_file_md  perchè viene parsato e convertito in HTML, mentre io voglio il raw
		//$static = $this->StaticModel->leggi_file_md($absoluteFname);

		$this->set('path', $fname);
		$this->set(compact(['static', 'title']));
	}

	public function delete(...$fname)
	{
		$this->Authorization->skipAuthorization();
		if ($this->StaticModel->delete($fname)) {
			$this->Flash->success('Pagina statica eliminata con successo');
			array_pop($fname);

			return $this->redirect(array_merge(['action' => 'index'], $fname));
		} else {
			$fnameStr = $this->StaticModel->combina_path($fname);

			return $this->Flash->error('Errore durante l\'eliminazione della pagina statica: ' . $fnameStr);
		}
	}

	public function add(...$path)
	{
		$this->Authorization->skipAuthorization();
		$sitedir = Configure::read('sitedir');
		$pathStr = $this->StaticModel->combina_path($path);
		$this->set('path', $path);

		//Salvo
		if ($this->request->is(['post', 'put'])) {
			$fname = $this->request->getData('fname');
			$static = $this->request->getData('static');
			$absoluteFname = WWW_ROOT . $sitedir . DS . 'static' . DS .  $pathStr . DS . $fname;

			//Se il file è già esistente ne devo creare un altro
			$i = 1;
			while (file_exists($absoluteFname)) {
				$parts = pathinfo($absoluteFname);
				$absoluteFname = $parts['dirname'] . DS . $parts['filename']  . "-$i." . $parts['extension'];
				$i++;
			}

			if ($this->StaticModel->save($absoluteFname, $static)) {
				$this->Flash->success('Pagina statica salvata con successo');

				return $this->redirect(array_merge(['action' => 'index'], $path));
			}
			$fnameStr = $this->StaticModel->combina_path($fname);

			return $this->Flash->error('Errore durante il salvataggio della pagina statica: ' . $fnameStr);
		}

		//Se c'è un file _template.md nella cartella lo uso come modello per la pagina
		$template = $this->StaticModel->getTemplate($path);
		$this->set(compact('template'));
	}
}
