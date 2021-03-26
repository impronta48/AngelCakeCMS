<?php
declare(strict_types=1);

/****
 *
 *
 * Import a drupal bikesquare site into cyclomap
 * example usage:
 * HTTP_HOST=cyclomap.bikesquare.test ./Console/cake import_drupal https://ebike.bikesquare.test
 *
 *
 **/

namespace App\Command;

use App\Model\Entity\Destination;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;
use Cake\Filesystem\Folder;
use Cake\Http\Client;
use Cake\http\Exception\NotFoundException;
use Cake\ORM\TableRegistry;
use Cake\Utility\Text;

class ImportDrupalCommand extends Command
{

	private $url;

	private $storagePath;

	private $io;

	private $articles;

	private $uploads;

	public function initialize(): void {
		parent::initialize();
		$this->articles = TableRegistry::getTableLocator()->get('Articles');
		$this->uploads = TableRegistry::getTableLocator()->get('Uploads');
		$this->storagePath  = WWW_ROOT . Configure::read('sitedir') .  '/articles';
	}

	public function execute(Arguments $args, ConsoleIo $io) {
		$drupalsite = $args->getArgument('drupal');
		$this->url = $drupalsite;

		$this->io = $io;
		$this->import_drupalNodes();
		$this->io->out("Inizio $drupalsite");
	}

	protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser {
		$parser->addArgument('drupal', [
		'help' => 'Either url of drupal site to import, eg: HTTP_HOST=cyclomap.bikesquare.test ./Console/cake import_drupal https://ebike.bikesquare.test',
		]);

		return $parser;
	}

	/**
	 * Importa i drupalNodes da un'installazione di drupal
	 *
	 * url     address of the server there the site is hosted (eg: http://ebike.bikesquare.eu)
	 *                 no trailing slash
	 * @return null
	 * @throws \Cake\http\Exception\NotFoundException When the view file could not be found
	 * or MissingViewException in debug mode.
	 */
	private function import_drupalNodes() {
		$nuovi_drupalNodes = [];
		$errori = [];

		if (empty($this->url)) {
			throw new NotFoundException();
		}

	  //creo la connessione al sito da cui voglio importare
		$HttpSocket = new Client();
	  // string query
		$response = $HttpSocket->get($this->url . '/export/nodes');

	  //leggo il dato
		$r = json_decode($response->getStringBody());
	  //debug($r->nodes);
	  //die;

		foreach ($r->nodes as $drupalNode) {
		  //leggo lo slug
			$drupalNode = $drupalNode->node;
			$slug = basename($drupalNode->slug); //Se lo slug è organizzato in cartelle prendo solo la parte finale
		  //$slug =$drupalNode->slug; //Se lo slug è organizzato in cartelle prendo solo la parte finale
			$this->io->out("---->Inizio Trattamento drupalNode $slug \n");

		  //Inizializzo newArticle
		  //Creo un nuovo record su DB e non c'è
			$newArticle = $this->Articles->findBySlug($slug)->first();

			if (empty($newArticle)) {
				$newArticle = $this->Articles->newEmptyEntity();
				$this->io->out('Creato nuovo record per ' . $slug);
			} else {
				$this->Articles->id = $newArticle->id;
				$this->io->out("Aggiorno il record {$this->Articles->id} : $slug");
			}

		  //Campi obbligatori
			$newArticle->slug = ltrim($slug, '/\\');
			$newArticle->title = $drupalNode->title;
			$newArticle->body = $drupalNode->body;
			$newArticle->modified = $drupalNode->changed;
		  //Campi semi-statici (o finti)
			$newArticle->created = $drupalNode->changed;
			$newArticle->published = true;
			$newArticle->user_id = 1;

		  //Se su drupal è impostata la destination la imposto anche io
			if (!empty($drupalNode->taxonomy_vocabulary_1)) {
				$destination = $this->Articles->Destinations->findByName($drupalNode->taxonomy_vocabulary_1)->first();
				if (empty($destination)) {
					$destination = new Destination([
					'slug' => strtolower(Text::slug($drupalNode->taxonomy_vocabulary_1)),
					'name' => $drupalNode->taxonomy_vocabulary_1,
					]);

					$destination  = $this->Articles->Destinations->save($destination);
					if (!$destination) {
							  $this->io->out('Errore durante il salvataggio della destination: ' . $drupalNode->taxonomy_vocabulary_1);
					}

					$this->io->out('Salvataggio OK di una nuova destination: ' . $drupalNode->taxonomy_vocabulary_1);
				  //Associo la destination all'articolo
				}
				$this->io->out('Associazione destination: ' . $destination->id);
				$newArticle->destination_id = $destination->id;
			}
		  //Ora salvo l'articolo completo
			$newArticle = $this->Articles->save($newArticle);
			if (!$newArticle) {
				$this->io->out('Errore durante il savataggio Articolo: ' . $drupalNode->title);
			} else {
				$this->io->out('Salvataggio Articolo OK: ' . $newArticle->id);
				if (isset($destination->slug)) {
					$dest_slug = $destination->slug . DS .  $newArticle->id . DS;
				} else {
					$dest_slug =  $newArticle->id . DS;
				}
			}

		  //importo gli allegati e le immagini
			if (isset($drupalNode->allegati)) {
				$this->importa_allegati($drupalNode->allegati, $dest_slug . 'files', $this->io);
			}
			if (isset($drupalNode->galleria)) {
				$this->importa_allegati($drupalNode->galleria, $dest_slug . 'galleria', $this->io);
			}
			if (isset($drupalNode->copertina)) {
				$this->importa_allegati($drupalNode->copertina, $dest_slug . 'copertina', $this->io);
			}

			$this->io->out("<----Fine Trattamento percorso $slug \n\r");
		  //break;
		}
	}

	/**
	 * Quando il campo è multilingua estrae la versione nella lingua richiesta
	 *  Es: "title":[{"value":"Tour in ebike Langhe a Barolo","lang":"it"},
	 *                             {"value":"Wine tour: Barolo, Pelaverga e Nascetta in ebike","lang":"en"}
	 *                            ]
	 * @field     campo drupal da importare
	 * @lang      lingua richiesta (es: 'it','en')
	 * @return \App\Command\valore nella lingua richiesta
	 *
	 */
	private function get_lang($field, $lang) {
		foreach ($field as $f) {
			if ($f->lang == $lang) {
				return $f->value;
			}
		}

		return null;
	}

	protected function showTable($data) {
		$this->Helper('table')->output($data);
	}

	private function getSlug($url) {
		return basename(parse_url($url, PHP_URL_PATH));
	}

	protected function importa_allegati($allegati, $folder) {
	  //Se non ci sono gli allegati esco subito
		if (empty($allegati)) {
			return;
		}

		if (is_string($allegati)) {
			$this->copiaSingolo($allegati, $folder);
		} elseif (is_array($allegati)) {      //Se è un campo multiplo
		//Se è un'immagine ha tre campi: src, alt, title
			foreach ($allegati as $allegato) {
				if (isset($allegato->src)) {
					$allegato = $allegato->src;
					$this->copiaSingolo($allegato, $folder);
				} elseif (is_string($allegato)) {
					$this->copiaSingolo($allegato, $folder);
				}
			}
		} elseif (isset($allegati->src)) {
			$allegato = $allegati->src;
			$this->copiaSingolo($allegato, $folder);
		}
	}

	private function copiaSingolo($allegato, $folder) {
		$originale = str_replace('yepp.drupalvm.test', 'yepp.it', $allegato);
		$this->io->out("file originale: $originale");
		$fname = basename($allegato);
	  //Tolgo eventuali %20 dal nome
		$fname = str_replace('%20', '', $fname);
		$dir = new Folder($this->storagePath . DS . $folder, true, 0777);
		$localFile =  $dir->pwd() . DS . "$fname";
		$this->io->out("file destinazione: $localFile");
		$this->copiaRemoto($originale, $localFile);
	}

	private function copiaRemoto($remoteFile, $localFile) {
	  //Se il file in locale c'è già non lo tiro giù

		if (!file_exists($localFile)) {
			if (!copy($remoteFile, $localFile)) {
				$this->io->out("Errore durante la copia $remoteFile in locale");
			} else {
				$this->io->out("Copia OK: $remoteFile");
			}
		} else {
			$this->io->out("Già presente $remoteFile");
		}
	}
}
