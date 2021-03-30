<?php
declare(strict_types=1);

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\ORM\TableRegistry;

// Remember to run this SQL query to rename the previous 'data' field to dataold and make a new one of type 'json'
//      ALTER TABLE `poi`
//      ADD `data` json NULL AFTER `namespace`,
//      CHANGE `data` `dataold` text COLLATE 'utf8mb4_unicode_ci' NULL AFTER `data`;

class ConvertDataToJsonCommand extends Command
{
	public function execute(Arguments $args, ConsoleIo $io) {
		$poitable = TableRegistry::getTableLocator()->get('Poi');
		$pois = $poitable->find();
		$io->out('Caricati Pois\nConversione campo data...');

		foreach ($pois as $poi) {
			if (is_null($poi->dataold)) {
				continue;
			}

			$data = @unserialize($poi->dataold);
			if (!$data) {
  				$data =  @unserialize(utf8_decode($poi->dataold)); // Decode first
				if (!$data) { // Still could not unserialize! Weird
					$io->out("[!] Impossibile deserializzare dati del POI #{$poi->id}");
					$io->out($poi->dataold);
					continue;
				}
  				$data = array_map('utf8_encode', $data ); // Encode data again
			}

			$poi->data = json_encode($data);
			$poitable->save($poi);
		}
		$io->out('Finito');
	}
}
