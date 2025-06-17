<?php

declare(strict_types=1);

namespace App\Command;

use App\Model\StaticModel;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;
use Cake\Filesystem\Folder;
use Cake\I18n\I18n;

class Static2articlesCommand extends Command
{

	private $io;

	protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
	{
		$parser->addArgument('folder', [
			'help' => 'Folder include in webroot/sitedir/static/',
		]);

		return $parser;
	}

	public function execute(Arguments $args, ConsoleIo $io)
	{
		$this->io = $io;
		$folder = $args->getArgument('folder');
		$sitedir = Configure::read('sitedir');

		$io->out('Inizio nella cartella ' . $folder);
		$name = WWW_ROOT . $sitedir . DS . 'static' . DS . $folder;
		$sm = new StaticModel();
		$dir = new Folder($name);
		$files = $dir->find('.*\.md');
		$risult = [];
		foreach ($files as $k => $f) {
			$risult[$k]['file']  = $f;
			$io->out('Lettura file: ' . $f);
			$risult[$k]['dati'] = $sm->leggi_file_md($name . DS . $f);
			if (!isset($risult[$k]['dati']['date'])) {
				$risult[$k]['dati']['date'] = null;
			}
		}

		$this->creaArticles($risult, $folder);
		$io->out('Finito.');
	}

	//Crea articoli a DB a partre dalle pagine statiche
	private function creaArticles($risult, $path)
	{
		$destinations['blog'] = 1;
		$destinations['_portfolio'] = 2;

		$this->loadModel('Articles');
		foreach ($risult as $r) {
			$article = $this->Articles->newEmptyEntity();
			$article->slug = basename($r['dati']['slug']);
			$this->io->out('Creazione Article : ' . $article->slug);

			$ex = $this->Articles->findBySlug($article->slug)->first();
			if ($ex) {
				$this->io->out('Articolo già esistente: ' . $article->slug);
				$article = $ex;
			}

			$article->title = $r['dati']['title'] ?? $article->slug;
			$article->description = substr($r['dati']['description'], 0, 255);
			$article->keywords = $r['dati']['keywords'] ?? '';
			$article->url_canonical = $r['dati']['canonical'] ?? '';
			$article->body = $r['dati']['body'];
			$article->modified = $r['dati']['date'];
			$article->published = true;
			$article->tag_string = $r['dati']['canonical'] ?? '';
			$article->user_id = 87;

			if (isset($destinations[$path])) {
				$article->destination_id = $destinations[$path];
			} else {
				$article->destination_id = null;
			}

			// Imposta la lingua italiana per l'articolo
			$this->Articles->setLocale('ita');
			if (strpos($path, '/eng') !== false) {
				$this->Articles->setLocale('eng');
			}

			if ($this->Articles->save($article)) {

				//La copertina va spostata da static/img a /articles/article-id/copertina
				if (!empty($r['dati']['copertina'])) {
					$r['dati']['copertina'] = ltrim($r['dati']['copertina'], '/');
					$oldCopertina = WWW_ROOT . $r['dati']['copertina'];
					$b = basename($oldCopertina);
					if (file_exists($oldCopertina) && isset($path)) {
						if (strlen($path)) {
							$newCopertina = WWW_ROOT . Configure::read('sitedir') . "/articles/$path/{$article->id}/copertina/";
						} else {
							$newCopertina = WWW_ROOT . Configure::read('sitedir') . "/articles/{$article->id}/copertina/";
						}
						$f = new Folder($newCopertina, true);
						rename($oldCopertina, $newCopertina . $b);
					}
				}
			}
		}
	}
}
