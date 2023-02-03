<?php
declare(strict_types=1);

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Filesystem\Folder;
use Cake\ORM\TableRegistry;

class NewDestinationFolderCommand extends Command
{

	public function execute(Arguments $args, ConsoleIo $io) {
		$io->out('Inizio.');
		$destinations = TableRegistry::getTableLocator()->get('Destinations');

		$destinationsList = $destinations->find('all');
		$baseFolder = WWW_ROOT . 'bikesquare/destinations/';
		foreach ($destinationsList as $d) {
			$newFolder = $baseFolder . $d->slug . DS . $d->id . '/copertina';
			$io->out('Sto trattando: ' . $d->slug);	
			$f = new Folder($newFolder, true);
			rename($baseFolder . $d->slug . '.jpg', $newFolder .  '/' . $d->slug . '.jpg');
		}
		$io->out('Finito.');
	}
}
