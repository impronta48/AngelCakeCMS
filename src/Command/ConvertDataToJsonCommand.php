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
		$issues = 0;
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
					$issues++;
					continue;
				}
  				$data = array_map('utf8_encode', $data ); // Encode data again
			}
			$poi->data = json_encode($data);
			$io->out($poi->data);
			$poitable->save($poi);
		}
		$io->out("Finito con $issues problemi");

		$io->out('\nConversione traduzioni campo data...');
		$i18ntable = TableRegistry::getTableLocator()->get('I18n');
		$localizations = $i18ntable->find()->where(['model' => 'Poi', 'field' => 'data']);
		$issues = 0;
		foreach ($localizations as $loc) {
			if (empty($loc->content) || $loc->content == 'a:0:{}') {
				$loc->content = null;
				$loc->setDirty('content');
				$i18ntable->save($loc);
				continue;
			}

			$data = @unserialize($loc->content);
			if (!$data) {
  				$data =  @unserialize(utf8_decode($loc->content)); // Decode first
				if (!$data) { // Try to fix field declared length
					$data = @unserialize(preg_replace_callback(
        				'!s:(\d+):"(.*?)";!s',
        				function($m){
        				    $len = strlen($m[2]);
        				    return "s:$len:\"{$m[2]}\";";
        				},
        				$data));
					if (!$data) {
						$io->out("[!] Impossibile deserializzare traduzione del POI #{$loc->foreign_key}");
						$io->out($loc->content);
						$issues++;
						continue;
					}
				} else { // Encode data again
  					$data = array_map('utf8_encode', $data );
				}
			}

			$loc->content = json_encode($data);
			$io->out($loc->content);
			$i18ntable->save($loc);
		}
		$io->out("Finito con $issues problemi");
	}
}
