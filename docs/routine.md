# Routine Di Manutenzione

## Descrizione Generale

La cartella:

```text
routine/
```

contiene script di manutenzione da eseguire manualmente da terminale.

## cleanup_uploads.php

`cleanup_uploads.php` cerca file fisici presenti in `uploads/` che non sono più referenziati dal database.

### - File Coinvolti

I file della routine sono:

```text
routine/cleanup_uploads.php
routine/cleanup_uploads_funcs.php
```

### - Controlli Eseguiti

La routine controlla:

- `uploads/pfp/` confrontata con `utenti.pfp`;
- `uploads/banner/` confrontata con `articoli.banner`;
- `uploads/file/` confrontata con `file_articoli.file_path`.

I file nascosti, come `.DS_Store`, vengono ignorati.

La routine serve come controllo aggiuntivo rispetto alla pulizia già eseguita durante il rifiuto delle proposte. È utile per individuare o rimuovere file orfani già presenti nel progetto.

### - Esecuzione

La routine è eseguibile solo da CLI.

Modalità predefinita, senza eliminazione:

```bash
php routine/cleanup_uploads.php
```

Equivale a:

```bash
php routine/cleanup_uploads.php --dry
```

Per eliminare davvero i file orfani:

```bash
php routine/cleanup_uploads.php --delete
```

Per visualizzare l'uso:

```bash
php routine/cleanup_uploads.php --help
```

Le opzioni `--dry` e `--delete` sono alternative. Se vengono passate insieme, la routine mostra l'uso ed esce con errore.

### - Dry Run

La modalità `--dry` non modifica il filesystem.

Mostra:

- numero di file fisici trovati;
- numero di path referenziati dal database;
- numero di file orfani;
- elenco dei file che verrebbero eliminati.

Questa modalità deve essere usata prima di `--delete` per controllare l'elenco dei file.

### - Delete

La modalità `--delete` elimina i file orfani trovati.

Un file viene considerato orfano quando:

- esiste fisicamente in una delle cartelle upload controllate;
- il suo percorso relativo non compare più nelle colonne DB corrispondenti.

La routine stampa anche eventuali file che non è riuscita a eliminare.

### Connessione Al Database

La routine non include direttamente:

```text
src/config/config.php
```

perché quel file apre subito la connessione al database e, da CLI, `localhost` può creare problemi di socket.

Per questo lo script carica solo:

```text
src/config/app.php
src/config/config.prod.php
```

oppure:

```text
src/config/app.php
src/config/config.test.php
```

e crea la connessione autonomamente. Se la connessione con `localhost` fallisce, prova anche `127.0.0.1`.
