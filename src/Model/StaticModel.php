<?php
declare(strict_types=1);

namespace App\Model;

use Authorization\Exception\ForbiddenException;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\Http\Exception\NotFoundException;
use Cake\Utility\Text;
use Cake\View\Exception\MissingTemplateException;

class StaticModel
{

	private $staticImgPath;

	public function __construct() {
	  //Imposto la cartella dove si trovano le immagini statiche
		$sitedir = Configure::read('sitedir');
		$this->staticImgPath = "/$sitedir/static/img/";
	}

	public function findAll($path = null) {
		$sitedir = Configure::read('sitedir');
		$name = WWW_ROOT .  $sitedir . DS . 'static' . DS . $path;

	  //Caricare il nostro frontmatter in modo che legga tutti i file nella cartella static
		$dir = new Folder($name);
		$t1 = microtime(true);
		$files = $dir->findRecursive('.*\.md');

		$risult = [];
		foreach ($files as $k => $f) {
		  //Ignoro i file che iniziano con underscore
			if ($this->file_begins_with_underscore($f)) {
				continue;
			}

			$risult[$k]['file']  = $f;
			$risult[$k]['dati'] = $this->leggi_file_md($f);

		  //Se nel file md metto sitemap=false questa pagina non finisce nella sitemap
			if (isset($risult[$k]['dati']['sitemap']) && $risult[$k]['dati']['sitemap'] == false) {
				unset($risult[$k]);
				continue;
			}

			if (!isset($risult[$k]['dati']['date'])) {
				$risult[$k]['dati']['date'] = null;
			}
			unset($risult[$k]['dati']['body']);
		}

	  //Ordino l'array dei risultati per il campo date invertito
	  if (isset($a['dat']['date'])){
		usort($risult, function ($a, $b) {
			return -1 * strcmp($a['dati']['date'], $b['dati']['date']);
		});
	  }
	  //$t2 = microtime(TRUE);
	  //dd($t2-$t1);

	  //dd($risult);
		return $risult;
	}

	private function file_begins_with_underscore($f) {
		$bname = basename($f);
		$dname = dirname($f);
		$parts = explode('/', $dname);
		foreach ($parts as $p) {
			if (strlen($p) > 0 and $p[0] == '_') {
				return true;
			}
		}

		return $bname[0] == '_';
	}

	public function get_path_from_file($fname) {
	  //Toglo il nome del file
		array_pop($fname);

		return $this->combina_path($fname);
	}

	public function combina_path($path) {
		$count = count($path);
		if (!$count) {
			return '';
		}
		if (in_array('..', $path, true) || in_array('.', $path, true)) {
			throw new ForbiddenException();
		}

		return implode('/', $path);
	}

	public function leggi_file_md($fname) {
	  //Visualizza la pagina che si chiama $name.md
		$parser = new \hkod\frontmatter\Parser(
			new \hkod\frontmatter\YamlParser(),
			new \hkod\frontmatter\MarkdownParser()
		);

		$dati = [];
		try {
			$file = new File($fname);

			$path_parts = pathinfo($fname);
			$bname = $path_parts['basename'];
			$fname = $path_parts['filename'];
			$path = $path_parts['dirname'];
			$miniPath = str_replace(WWW_ROOT . Configure::read('sitedir') . '/static', '', $path);
			$miniPath = str_replace(Configure::read('sitedir') . '/static', '', $miniPath);

			$contents = $file->read();
			$result = $parser->parse($contents);
			$body = $result->getBody();
			$variabili = $result->getFrontmatter();

			$variabili['id'] = $fname;

		  /*DESCRIPTION*/
			if (!isset($variabili['description'])) {
				$description = Text::truncate(
					strip_tags($body),
					200,
					['ellipsis' => '...']
				);
				//dd($description);
				$variabili['description'] = $description;
			}

		  /*TITLE */
			if (!isset($variabili['title'])) {
				$title = str_replace('-', ' ', $fname);
				$variabili['title'] = $title;
			}

			$slug = ltrim($miniPath . DS . $fname, '/');
			$variabili['slug'] = $slug;

		  /*CANONICAL*/
			if (!isset($variabili['canonical'])) {
				if (isset($_SERVER['SERVER_PROTOCOL'])) {
					$protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === 0 ? 'http://' : 'https://';
				} else {
					$protocol = '//';
				}
				$canonical = $protocol . env('HTTP_HOST') . '/' . $slug;
				$variabili['canonical'] = $canonical;
			}

		  /*COPERTINA*/
			if (isset($variabili['copertina'])) {
			  //Se la copertina inizia con http non faccio nulla
				if (strpos($variabili['copertina'], 'http') !== 0) {
					$variabili['copertina'] = $this->staticImgPath . $variabili['copertina'];
				}
			}
			$variabili['body'] = $body;

			$file->close();

			return $variabili;
		} catch (MissingTemplateException $exception) {
			if (Configure::read('debug')) {
				throw $exception;
			}
			throw new NotFoundException();
		}
	}

	public function find($path, $limit) {
		$simplename = str_replace('/', '-', $path);

		$risult = Cache::read("static_{$simplename}_{$limit}", 'static');
		if (empty($risult)) {
		  //Caricare il nostro frontmatter in modo che legga tutti i file nella cartella static
			$dir = new Folder($path);
			$files = $dir->find('.*\.md');

		  //fai un foreach sui file e per ogni file chiami la leggi_file_md
		  //ti metti il risultato in qualche variabile e lo passi alla view
		  //Prendi solo i primi 5
		  //dd($files);
			$risult = [];
			$i = 0;
			foreach ($files as $k => $f) {
				//Se inizia con _ ignoro
				if ($f[0] != '_') {
					$risult[$k]['file']  = $f;
					$risult[$k]['dati'] = $this->leggi_file_md($path . DS . $f);
					if (!isset($risult[$k]['dati']['date'])) {
						$risult[$k]['dati']['date'] = null;
					}
				  //Mi devo fermare quando raggiungo il limite
					if ($i > $limit) {
						break;
					}
				}
			}

		  //Ordino l'array dei risultati per il campo date invertito
			usort($risult, function ($a, $b) {
				return -1 * strcmp($a['dati']['date'], $b['dati']['date']);
			});

			if (!empty($limit)) {
				  $risult = array_slice($risult, 0, $limit);
			}
			Cache::write("static_{$simplename}_{$limit}", $risult, 'static');
		}

		return $risult;
	}

  //Toglie il basepath da un path complessivo

	private function relativePath($base, $full) {
		return trim(str_replace($base, '', $full));
	}

	public function get($absoluteFname) {
		$file = new File($absoluteFname);
		$static = $file->read();
		$file->close();

		return $static;
	}

	public function save($absoluteFname, $static) {

		$file = new File($absoluteFname);
		$result = $file->write($static);
		Cache::clear('static');
		$file->close();

		return $result;
	}

	public function delete($fname) {
	  //Sposto in _trash
		$sitedir = Configure::read('sitedir');
		$absoluteFname = WWW_ROOT . $sitedir . DS . 'static/' . $this->combina_path($fname);
		$baseName = array_pop($fname);
		$path = WWW_ROOT . $sitedir . DS . 'static/' . $this->combina_path($fname);
		$trash = new Folder("$path/_trash", true);
		Cache::clear('static');

		return rename($absoluteFname, $trash->pwd() . DS . $baseName);
	}

	public function getTemplate($path) {
		$sitedir = Configure::read('sitedir');
		$absolutePath = WWW_ROOT . $sitedir . DS . 'static/' . $this->combina_path($path);
		$f = new Folder($absolutePath);
		$t = $f->find('_template.md');
		$template = null;

		if (!empty($t)) {
			$file = new File($f->addPathElement($absolutePath, $t[0]));
			$template = $file->read();
			$file->close();
		}

		return $template;
	}
}
