<?php

namespace App\Command;

use Cake\Console\Arguments;
use Cake\Command\Command;
use Cake\Console\ConsoleIo;
use Cake\ORM\TableRegistry;
use Cake\Http\Client;


class Participants2igasCommand extends Command
{

  public function execute(Arguments $args, ConsoleIo $io)
  {
    //TODO: TRASFORMARE IN PARAMETRO
    $event_id = 1;
    //$igas = "https://igas.impronta48.it/bikesquare";
    $igas = "http://crm.drupalvm.test";
    $tag  = "formazione-2021";

    $io->out('Inizio.');
    $participantsT = TableRegistry::getTableLocator()->get('Participants');
    $http = new Client();

    $ps = $participantsT->find('all')->where(['event_id' => $event_id]);

    // https://stackoverflow.com/questions/5517255/remove-style-attribute-from-html-tags
    foreach ($ps as $p) {
      $io->out("$igas/persone/update+" . $p->email);

      $response = $http->post("$igas/persone/update", [
        'EMail' =>  trim($p->email),
        'Nome' =>  trim($p->name),
        'Cognome' =>  trim($p->surname),
        'Cellulare' =>  $p->tel,
        'DisplayName' =>  trim($p->name) . " " . trim($p->surname),
        'DataDiNascita' => (!empty($p->dob)) ? $p->dob->format('y-m-d') : null,
        'Citta' =>  $p->city,
        'Nazione' =>  "IT",
        'IM' =>  $p->facebook,
        'Nota' =>  $p->destination . PHP_EOL . $p->experience . PHP_EOL . $p->past,
        'tag_list' => $tag,
      ]);

      $io->out($response->getReasonPhrase());
    }
    $io->out('Finito.');
  }
}
