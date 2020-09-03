<?php
namespace App\Command;

use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\ORM\TableRegistry;

class RemoveStyleCommand extends Command
{
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $io->out('Inizio.');
        $articles = TableRegistry::getTableLocator()->get('Articles');

        $articlesList = $articles->find('all');

        // https://stackoverflow.com/questions/5517255/remove-style-attribute-from-html-tags
        foreach ($articlesList as $a)
        {
            $a->body = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $a->body);
            if ($articles->save($a))
            {
                $io->out($a->slug . " aggiornato con successo");
            }
            else{
                $io->out($a->slug . " errore durante l'aggiornamento");
            }
        }
        $io->out('Finito.');
    }
}
