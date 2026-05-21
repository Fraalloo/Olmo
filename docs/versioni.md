**ELENCO VERSIONI**

**Versione v0.1.0**

Data 23/03/2026

Prima versione di sviluppo comprendente documentazione sul DB, schema CRUD e design.

**Versione v0.1.1**

Data 14/04/2026

Versione comprendente rettifiche di documentazione e sviluppo del sistema di autenticazione al sito, con connessione al rispettivo DB.

**Versione v0.1.2**

Data 27/04/2026

Versione comprendente implementazione della home page, con: mappa usando LeafletJS e OpenMap, ricerca con Nominatim, paginazione, elenco degli articoli e layout responsive.

Aggiunta, inoltre, una directory con dati di testing.

**Versione v0.1.3**

Data 04/05/2026

Versione comprendente l’implementazione della pagina dell’articolo, le funzionalità da admin e la gestione del proprio profilo.

È stato fatto un cambiamento al DB schema, descritto nella rettifica della versione 0.1.3.

**Versione v0.1.4**

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