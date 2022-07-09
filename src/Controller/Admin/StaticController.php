<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;
use App\Model\StaticModel;
use Cake\Core\Configure;
use Cake\Filesystem\Folder;

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

	public function initialize(): void {
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
	public function index(...$path) {
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

  //Legge una cartella remota di webDav e aggiorna la cartella static del sito corrente

	public function getRemote() {
		$this->Authorization->skipAuthorization();
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

	public function edit(...$fname) {
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

	public function delete(...$fname) {
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

	public function add(...$path) {
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
