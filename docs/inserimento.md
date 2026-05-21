# Inserimento Articoli

## Descrizione Generale

L'inserimento di un nuovo articolo è gestito dai file:

```text
src/pages/insert/insert_page.php
src/pages/insert/insert_page.js
src/pages/insert/insert.php
src/pages/insert/insert_page.css
```

La pagina è accessibile solo agli utenti autenticati. Se l'utente deve cambiare password, viene reindirizzato al profilo prima di poter inserire contenuti.

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

## Stato Del Nuovo Articolo

Un articolo appena inserito da un utente viene salvato come proposta in attesa:

```sql
id_admin = NULL
is_active = 0
is_hidden = 0
versione = 1
```

Dopo l'approvazione da parte di un admin, la versione può diventare attiva.