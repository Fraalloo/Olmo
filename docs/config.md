# Configurazione

## Descrizione Generale

La configurazione centralizza costanti applicative, percorsi e parametri di connessione al database.

I file principali sono:

```text
src/config/app.php
src/config/config.php
src/config/config.prod.php
src/config/config.test.php
```

## app.php

`app.php` contiene costanti condivise dall'applicazione.

Costanti generali:

- `APP_NAME`
- `CURR_VERS`
- `DEBUG`
- `PROJECT_ROOT`

Foto profilo:

- `DEFAULT_PFP`
- `DEFAULT_PFP_PATH`
- `UPLOAD_PFP`
- `UPLOAD_PFP_PATH`
- `MAX_PFP_SIZE`
- `ALLOWED_PFP_MIME`

Banner articoli:

- `UPLOAD_BANNER`
- `UPLOAD_BANNER_PATH`

File allegati agli articoli:

- `UPLOAD_FILE`
- `UPLOAD_FILE_PATH`
- `MAX_ARTICLE_FILE_SIZE`
- `ALLOWED_ARTICLE_FILE_MIME`

I MIME ammessi per profilo e banner sono JPG, PNG e WEBP. Gli allegati articolo accettano PDF, TXT, JPG, PNG e WEBP.

Se `DEBUG` è attivo, PHP mostra errori e warning.

## config.php

`config.php` sceglie quale configurazione database caricare:

1. se esiste `config.prod.php`, usa quella;
2. altrimenti usa `config.test.php`.

Poi apre la connessione MySQLi e rende disponibile `$conn`.

## config.prod.php E config.test.php

Questi file contengono i parametri di connessione:

- host;
- utente;
- password;
- nome database.

La separazione permette di usare credenziali diverse tra ambiente locale, test e produzione senza modificare la logica delle pagine.
