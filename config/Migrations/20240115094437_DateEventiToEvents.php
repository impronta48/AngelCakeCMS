<?php
declare(strict_types=1);

use Migrations\AbstractMigration;
use Cake\Datasource\ConnectionManager;

// TODO: non è chiaro come usare l'helper 'Table' con una connessione ad hoc
// (per fare tutte le modifiche all'interno di una transaction)
// uso statement sql diretti

class DateEventiToEvents extends AbstractMigration
{
    public function up(): void
    {
        $connection = ConnectionManager::get('default');
        $connection->transactional(function ($connection) {
            // aggiungi colonna percorso_id
            $connection->execute('ALTER TABLE events ADD COLUMN percorso_id INTEGER');
            $connection->execute('CREATE INDEX events_percorso_idx ON events(percorso_id)');

            // carica date_eventi esistenti e genera eventi corrispondenti
            $date_eventi = $connection->execute('SELECT * FROM date_eventi')->fetchAll('assoc');
            foreach($date_eventi as $row) {
                $connection->execute('INSERT INTO events (start_time, end_time, min_year, max_year, percorso_id) VALUES (?,?,?,?,?)', [
                    $row['data_evento'].' 00:00:00',
                    $row['data_evento'].' 23:59:59',
                    date('Y',strtotime($row['data_evento'])),
                    date('Y',strtotime($row['data_evento'])),
                    $row['percorso_id']
                ]);
            }

            // elimina tabella date_eventi
            $connection->execute('DROP TABLE IF EXISTS date_eventi');
        });
    }

    public function down(): void
    {
        $connection = ConnectionManager::get('default');
        $connection->transactional(function ($connection) {
            // carica gli eventi con percorso != null
            $eventi = $connection->execute('SELECT * FROM events WHERE percorso_id IS NOT NULL')->fetchAll('assoc');
            // rimuovi tutti gli eventi con percorso_id != null
            $connection->execute('DELETE FROM events WHERE percorso_id IS NOT NULL', []);
            // rimuovi colonna percorso_id da events
            $connection->execute('ALTER TABLE events DROP COLUMN percorso_id');
            // crea tablella date_eventi + fill
            $connection->execute('CREATE TABLE date_eventi (id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, percorso_id INTEGER NOT NULL, data_evento DATE NOT NULL)');
            foreach($eventi as $evento) {
                $connection->execute('INSERT INTO date_eventi (percorso_id,data_evento) VALUES (?,?)', [
                    $evento['percorso_id'],
                    date('Y-m-d', strtotime($evento['start_time'])),
                ]);    
            }
            
        });
    }
}
