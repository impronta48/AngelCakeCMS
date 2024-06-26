## Mappa Generale

### Inbox
- [ ] correzione pagina edit categorie
- [ ] mostrare l'altimetria nel percorso
- [ ] mettere un link a destra dello slider che dica [mostra tutti]
- [ ] mettere dei colori furbi per i percorsi che sono nella stessa destination
- [ ] rifare le icone di accessibility in stile moderno @matteo
- [ ] fare un logo per accessibility @matteo
- [ ] migliorare icona "sono qui" (usare quella di rent?) @matteo
- [ ] rivedere il css di default per i siti cyclomap standard

### Doing
- [ ] anche il percorso deve prendere dalla cartografia generale
- [ ] chiampo -> pulire i kml (rimuovere i poi)
- [ ] pagina dei poi filtrabile + description categoria

### Done
- [x] quando apro destinations mostrare contenuti saggi a sinistra
- [x] legenda: spegni percorsi
- [x] fumetto su percorsi (titolo e tutto)
- [x] fumetto su poi (titolo)
- [x] fumetto evoluto (poi + foto + descrizione + link)
- [x] geocoding
- [x] aggiungere un punto alla mappa con la richiesta dell'utente
- [x] slider con le schede dei poi
- [x] slider con le schede dei percorsi
- [x] se faccio click sui poi selezionare sulla mappa (e viceversa)
- [x] correggere le dimensioni dei select dei filtri nel caso di scritte lunghe
- [x] gestire traduzione tutte le destinationi e percorsi, dove vuoi andare
- [x] verifica perchè scrive destinazione nell'url se non c'è niente di selezionato
- [x] this.makeSeoTitle is not a function
- [x] se ricarico senza destination trova la destination vicino a me
- [x] clic su poi apre anche fumetto su mappa
- [x] quando apro un fumetto porta la pagina alla mappa
- [x] clic su percorso in slider mostra su mappa
- [x] passare all'url le categorie selezionate in legenda
- [x] verificare i centrodi dei percorsi a caso
- [x] fare la linea selezionata più spessa
- [x] aprire fumetto su click sul percorso
- [x] cambiare fumetto su linea percorso
- [x] scelta sfondo mappa (topo, open, cyclo)
    - [x] rendere parametrico da settings
- [x] rendere parametrico (settings.php) percorso style    
- [x] se c'è una sola destinazione selezionarla di default
- [x] correggere font standard, non solo eon
- [x] mettere label arrotondate per i percorsi su mappa
- [x] portare i css nella mappa sui siti dove non è integrata
- [x] se manca la foto del punto o del percorso usare il default (copertina?)
- [x] se attivita nell'url è vuoto mettere i noleggiatori (settings)
- [x] mappa: zoom iniziale sbagliato
- [x] mappa: cerca lungo mette slider

#### Gold Silver Bronze
- [ ] pagina destinations/index -> gold silver bronze
- [ ] mappa - >gold silver bronze (usare una class ed un css)
- [ ] fare spiegazione dei livelli di servizio per il pubblico

### Migliorie  (nice to have)
- [ ] far saltare il pin quando faccio clic
- [ ] quando apro la mappa senza destination dovrei vedere percorsi random (il random non funziona bene)
- [ ] mi carica la mappa-generale di ebike2021 invece di quella di cyclomap (capire perchè)
- [ ] migliorare lo skeleton (le colonne non corrispondono e sparisce troppo presto)
- [ ] mettere degli skeleton mentre si carica la legenda
- [ ] ordinare le categorie per priorità (es: punti di noleggio per primi)
- [ ] mettere un contatore che mostra quanti elementi ci sono (poi, percorsi, etc)
- [ ] aggiungere la funzione "pulisci KML" allo script fix_percorsi_coords
- [ ] aggiungere la funziona calcola altimetrica (google, osm?) allo script fix_percorsi_coords /https://openrouteservice.org/
- [ ] aggiungere la funziona crea gpx allo script fix_percorsi_coords
- [ ] file un tiles cache su nginx invece di php


## Idee
- [ ] Valutare integrazione di google pay
- [ ] usare un altro geocoder (cineca?)
- [ ] aggiungere una funzionalità di routing per biciclette usando b-router ( --> mobility48)
- [ ] Tiles offline per l'app https://docs.protomaps.com/pmtiles/leaflet


