# Gestione Profilo

## Descrizione Generale

La gestione del profilo permette agli utenti autenticati di visualizzare le proprie informazioni e modificare alcuni dati dell'account.

I file principali sono:

```text
src/pages/profile/profile.php
src/pages/profile/modifica_profile.php
src/pages/profile/update_profile.php
```

## Visualizzazione Profilo

`profile.php` mostra:

- nome utente;
- foto profilo;
- ruolo, cioè utente normale o admin;
- data di registrazione.

La pagina contiene anche il collegamento alla modifica del profilo. Se l'utente deve cambiare obbligatoriamente la password, viene mostrato un avviso e le altre sezioni del sito vengono bloccate tramite `auth_guard.php`.

## Modifica Profilo

`modifica_profile.php` contiene il form di modifica.

L'utente può aggiornare:

- nome utente;
- foto profilo;
- password.

Quando il cambio password è obbligatorio, il form viene limitato alla sola modifica della password.

## Aggiornamento Dati

`update_profile.php` riceve i dati del form e aggiorna il database.

I controlli server-side riguardano:

- presenza dell'utente in sessione;
- esistenza dell'utente nel database;
- lunghezza minima del nome utente;
- unicità del nome utente;
- password attuale corretta quando richiesta;
- conferma della nuova password;
- nuova password diversa dalla precedente;
- tipo MIME della nuova foto profilo.

Dopo un aggiornamento riuscito vengono aggiornate anche le variabili di sessione, così nome utente e foto profilo restano coerenti nell'interfaccia.

## Foto Profilo

La foto profilo è salvata nel campo `pfp` della tabella `utenti` come percorso relativo.

Il caricamento prevede:

- controllo del tipo MIME;
- accettazione di JPG, PNG e WEBP;
- generazione di un nome file univoco tramite `uniqid("pfp_", true)`;
- salvataggio in `uploads/pfp/`;
- aggiornamento del percorso nel database.

Se l'utente non ha una foto profilo personale, il codice usa l'immagine predefinita indicata da `DEFAULT_PFP`.

## Cambio Password Obbligatorio

Il campo `must_change_password` della tabella `utenti` indica se l'utente deve cambiare password prima di usare il sito.

Il flusso è:

1. l'utente effettua il login;
2. il sistema salva in sessione `must_change_password`;
3. se il valore è attivo, l'utente viene reindirizzato al profilo;
4. l'utente imposta una nuova password;
5. il database viene aggiornato con `must_change_password = 0`;
6. la sessione viene aggiornata e l'accesso alle altre pagine torna consentito.

## Utility Di Protezione

La protezione delle pagine è centralizzata in:

```text
src/utils/auth_guard.php
```

Le funzioni principali sono:

- `require_login()`;
- `require_admin()`;
- `require_password_change_if_needed()`.