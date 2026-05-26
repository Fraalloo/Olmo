# ELENCO VERSIONI

## Versione v0.1.0

Data 23/03/2026

Prima versione di sviluppo comprendente documentazione sul DB, schema CRUD e design.

## Versione v0.1.1

Data 14/04/2026

Versione comprendente rettifiche di documentazione e sviluppo del sistema di autenticazione al sito, con connessione al rispettivo DB.

## Versione v0.1.2

Data 27/04/2026

Versione comprendente implementazione della home page, con: mappa usando LeafletJS e OpenMap, ricerca con Nominatim, paginazione, elenco degli articoli e layout responsive.

Aggiunta, inoltre, una directory con dati di testing.

## Versione v0.1.3

Data 04/05/2026

Versione comprendente l’implementazione della pagina dell’articolo, le funzionalità da admin e la gestione del proprio profilo.

È stato fatto un cambiamento al DB schema, descritto nella rettifica della versione 0.1.3.

## Versione v0.1.4

Data 19/05/2026

Versione comprendente l'implementazione della pagina di inserimento articoli, con:

- titolo, tipo e descrizione;
- banner opzionale;
- allegati multipli;
- link multipli;
- coordinate manuali;
- selezione coordinate tramite mappa Leaflet;
- validazione server-side di campi, link, coordinate e upload.

Sono state aggiornate anche le risorse di test:

- record SQL in `test/db`;
- file fisici in `test/uploads`;
- banner, allegati e foto profilo con nomi generati secondo la logica applicativa.

È stato aggiornato lo script di inizializzazione per permettere l'import opzionale dei record di test.

È stato aggiunto il documento `inserimento.md` per descrivere il flusso di creazione degli articoli.

## Versione v0.1.5

Data 21/05/2026

Versione comprendente l'implementazione della modifica degli articoli, con:

- pagina `pages/edit` coerente con la struttura di `pages/insert`;
- form precompilato con dati, coordinate, banner, file e link della versione attiva;
- mantenimento o rimozione dei file esistenti tramite checkbox;
- mantenimento o rimozione dei link esistenti tramite checkbox;
- possibilità di caricare nuovi allegati e aggiungere nuovi link;
- creazione di una nuova versione in attesa di convalida, senza modificare direttamente la versione pubblicata;
- creazione dei nuovi record in `file_articoli` e `link_articoli` per la versione proposta.

Sono state aggiornate anche la pagina dettaglio articolo, la documentazione di inserimento/modifica, la documentazione database e la documentazione admin.

## Versione v0.2.1

Data 25/05/2026

Aggiunti controlli in reject.php sulle risorse presenti nel file system.

Creata la directory /routine per l'esecuzione di script di controllo sul web server.

## Versione v0.2.2

Data 26/05/2026

Aggiunto un pulsante per copiare il contenuto di un articolo.

Aggiunto un workflow per il controllo della sintassi PHP tramite linter.

Aggiunto un workflow per il controllo della sintassi JavaScript tramite linter.

Aggiunto un workflow per il controllo dello schema SQL e dei dati di test tramite importazione in un database MariaDB temporaneo.

Aggiunto un workflow per il controllo della sintassi Markdown tramite linter.
