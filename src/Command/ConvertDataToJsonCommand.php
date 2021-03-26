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
//      CHANGE `data` `dataold` text COLLATE 'latin1_swedish_ci' NULL AFTER `data`;

class ConvertDataToJsonCommand extends Command
{
	public function execute(Arguments $args, ConsoleIo $io) {
		$poitable = TableRegistry::getTableLocator()->get('Poi');
		$pois = $poitable->find();
		$io->out('Caricati Pois\nConversione campo data...');

		foreach ($pois as $poi) {
			$data = unserialize($poi->dataold);
			if (!$data) {
				$io->out("[!] Impossibile deserializzare dati del POI #{$poi->id}");
				$io->out($poi->dataold);
			} else {
				$poi->data = json_encode($data);
				$poitable->save($poi);
			}
		}
		$io->out('Finito');
	}
}
