# Conversione data in json
Il comando `ConvertDataToJson` converte il campo `data` dentro ai Poi in json, prima deserializzando e poi riserializzando in json.

## Preparazione
Per evitare perdita di dati, rinominare la colonna `data` in `dataold` e aggiungere una nuova colonna `data` di tipo `json` che possa essere `NULL`:
```sql
ALTER TABLE `poi`
ADD `data` json NULL AFTER `namespace`,
CHANGE `data` `dataold` text COLLATE 'utf8mb4_unicode_ci' NULL AFTER `data`;
```

## Esecuzione
Eseguire il comando con `bin/cake convert_data_to_json` (specificando `HTTP_HOST=www.usedsite.example` se necessario).

## Checkup
Controllare che il contenuto della colonna `data` sia corretto. Eventuali errori nella deserializzazione saranno stampati a terminale.
Nel caso tutti i dati siano stati convertiti correttamente, e' possibile rimuovere la colonna `dataold`.