# Inserimento E Modifica Articoli

## Descrizione Generale

L'inserimento di un nuovo articolo è gestito dai file:

```text
src/pages/insert/insert_page.php
src/pages/insert/insert_page.js
src/pages/insert/insert.php
src/pages/insert/insert_page.css
```

La modifica di un articolo esistente è gestita dai file:

```text
src/pages/edit/edit_page.php
src/pages/edit/edit_page.js
src/pages/edit/edit.php
src/pages/edit/edit_page.css
```

Le pagine sono accessibili solo agli utenti autenticati. Se l'utente deve cambiare password, viene reindirizzato al profilo prima di poter proporre contenuti.

## Form Di Inserimento

`insert_page.php` mostra il form con:

- titolo;
- tipo articolo;
- descrizione;
- coordinate opzionali;
- mappa Leaflet opzionale per scegliere le coordinate;
- banner opzionale;
- allegati multipli;
- link multipli.

I tipi articolo vengono letti dalla tabella `tipi_articoli`.

## Form Di Modifica

`edit_page.php` mostra un form con la stessa struttura della pagina di inserimento, ma precompilato con i dati della versione attiva dell'articolo:

- titolo;
- tipo articolo;
- descrizione;
- coordinate;
- banner;
- file allegati;
- link.

La pagina è raggiungibile dal pulsante `Modifica` nel dettaglio articolo.

La modifica è permessa solo per articoli:

- approvati;
- attivi;
- non hidden.

L'invio non modifica direttamente la versione pubblicata. Viene invece creata una nuova versione nello stesso `id_gruppo_articolo`, in attesa di convalida admin.

## Coordinate

Le coordinate possono essere inserite in due modi:

- manualmente nei campi latitudine e longitudine;
- cliccando sulla mappa Leaflet.

La mappa è nascosta di default e può essere mostrata o nascosta tramite pulsante.

Il pulsante `Rimuovi coordinate` svuota i campi e rimuove il marker dalla mappa.

Lato server, le coordinate sono valide solo se:

- sono entrambe presenti oppure entrambe assenti;
- la latitudine è compresa tra `-90` e `90`;
- la longitudine è compresa tra `-180` e `180`.

## Upload

Il banner accetta immagini:

- JPG;
- PNG;
- WEBP.

Gli allegati accettano:

- PDF;
- TXT;
- JPG;
- PNG;
- WEBP.

I file vengono salvati nelle directory configurate in `app.php`:

```text
uploads/banner/
uploads/file/
```

Il nome fisico viene generato tramite `uniqid()` con prefisso:

- `banner_` per i banner;
- `file_` per gli allegati.

Per gli allegati viene salvato anche il nome originale nel campo `nome_originale`.

In modifica:

- il banner attuale può essere mantenuto, rimosso o sostituito;
- i file già associati possono essere mantenuti tramite checkbox;
- i nuovi allegati vengono caricati come nell'inserimento;
- i file mantenuti generano nuovi record in `file_articoli` collegati alla nuova versione, ma possono puntare allo stesso file fisico della versione precedente;
- i link già associati possono essere mantenuti tramite checkbox;
- i nuovi link vengono aggiunti alla lista della nuova versione.

## Validazione Server-Side

`insert.php` controlla:

- metodo `POST`;
- sessione valida;
- titolo, descrizione e tipo articolo;
- lunghezza massima del titolo;
- esistenza del tipo articolo;
- validità delle coordinate;
- validità dei link;
- dimensione massima dei file;
- MIME reale dei file;
- scrivibilità delle directory upload.

`edit.php` applica gli stessi controlli sui dati modificati e verifica anche che l'articolo sorgente sia modificabile.

## Transazione

L'inserimento avviene in una transazione MySQL:

1. verifica del tipo articolo;
2. eventuale salvataggio del banner;
3. creazione del gruppo articolo;
4. creazione della versione in `articoli`;
5. salvataggio degli allegati in `file_articoli`;
6. salvataggio dei link in `link_articoli`;
7. commit finale.

In caso di errore viene eseguito il rollback e i file già creati durante il tentativo vengono eliminati.

La modifica avviene a sua volta in transazione:

1. verifica dell'articolo sorgente;
2. verifica del tipo articolo;
3. eventuale salvataggio del nuovo banner;
4. calcolo della prossima versione con `MAX(versione) + 1`;
5. creazione della nuova riga in `articoli`;
6. copia dei record dei file mantenuti;
7. salvataggio dei nuovi allegati;
8. salvataggio dei link mantenuti e dei nuovi link;
9. commit finale.

In caso di errore viene eseguito il rollback e vengono eliminati solo i file fisici creati durante quel tentativo.

## Stato Del Nuovo Articolo

Un articolo appena inserito da un utente viene salvato come proposta in attesa:

```sql
id_admin = NULL
is_active = 0
is_hidden = 0
versione = 1
```

Dopo l'approvazione da parte di un admin, la versione può diventare attiva.

## Stato Di Una Modifica

Una modifica proposta viene salvata come nuova versione dello stesso gruppo articolo:

```sql
id_gruppo_articolo = gruppo della versione sorgente
versione = MAX(versione) + 1
id_admin = NULL
is_active = 0
is_hidden = 0
```

Fino alla convalida, la versione attiva precedente resta visibile in Home e nel dettaglio pubblico.
