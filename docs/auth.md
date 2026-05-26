# Autenticazione

## Descrizione Generale

Il sistema di autenticazione gestisce:

- accesso;
- registrazione;
- sessione utente;
- logout;
- distinzione tra utenti normali e admin;
- obbligo di cambio password per account temporanei.

I file principali sono:

```text
src/auth/access.php
src/auth/login.php
src/auth/signup.php
src/auth/logout.php
src/utils/auth_guard.php
```

## Landing Page

`index.php` è la prima pagina visibile all'utente e consente di accedere, registrarsi o proseguire verso la Home se la sessione è già attiva.

## access.php

`access.php` contiene il layout comune per login e registrazione.

La modalità visualizzata viene gestita tramite `$_SESSION["access_mode"]` e tramite il parametro `mode` in query string.

## login.php

Il login:

1. riceve nome utente e password;
2. cerca l'utente nella tabella `utenti`;
3. verifica la password con `password_verify()`;
4. salva in sessione:
   - `user_id`;
   - `username`;
   - `is_admin`;
   - `pfp`;
   - `must_change_password`;
5. reindirizza alla Home o al profilo se il cambio password è obbligatorio.

Se l'utente non ha una foto profilo personale, in sessione viene usato `DEFAULT_PFP`.

## signup.php

La registrazione crea sempre utenti normali. Il campo `is_admin` resta al valore di default `0`.

Il form richiede:

- nome utente;
- password;
- conferma password;
- foto profilo opzionale.

I controlli principali sono:

- campi obbligatori;
- nome utente più lungo di tre caratteri;
- password e conferma coincidenti;
- nome utente non già esistente;
- dimensione massima della foto profilo;
- MIME reale della foto profilo;
- salvataggio nella cartella `uploads/pfp/`.

La password viene salvata come hash tramite `password_hash()`.

Se il caricamento della foto profilo riesce ma l'inserimento nel database fallisce, il file appena creato viene eliminato per evitare file orfani.

## logout.php

`logout.php` svuota la sessione, la distrugge e reindirizza l'utente alla landing page.

## Ruoli

Il ruolo dipende dal campo `is_admin`:

- `0`: utente normale;
- `1`: amministratore.

Gli utenti normali possono proporre contenuti. Gli admin possono convalidare, rifiutare, nascondere, ripristinare contenuti e gestire ruoli utente dalla dashboard.

## Admin Iniziale

Lo schema crea l'utente `DBAdmin` con `id_utente = 1`, ruolo admin e `must_change_password = 1`.

Password iniziale:

```text
123!OlmoOlmo!321
```

Hash salvato nello schema:

```text
$2y$12$MFMnmfE16pJ8b5w30SLBoepi3T4BRhTjhvK.gTEyutNqKr9C/XuVS
```

Al primo accesso è richiesto il cambio password.
