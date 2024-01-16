<?php
declare(strict_types=1);

use Migrations\AbstractMigration;
use Cake\Datasource\ConnectionManager;

class AddEventoCosto extends AbstractMigration
{
    public function up(): void
    {
        // x semplicità uso oggetto connection
        try {
            $connection = ConnectionManager::get('default');
            $connection->execute('ALTER TABLE events ADD COLUMN cost DECIMAL(10,2)');
        }
        catch(Exception $e) {
            // se fallisce non mi interessa (vuol dire che la colonna esiste già)
        }
    }

    public function down(): void {
        // non faccio nulla di proposito: nella logica è già gestito il costo
        // (quindi alcuni db / alcune installazioni ce l'hanno)
    }
}
