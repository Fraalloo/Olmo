# Gestione Profilo

## Descrizione generale

La gestione del profilo permette agli utenti autenticati di visualizzare le proprie informazioni personali e modificare alcuni dati dell’account.

Il profilo è separato in tre file principali:

```text
pages/profile/profilo.php
pages/profile/modifica_profilo.php
pages/profile/update_profile.php
```

Questa separazione permette di distinguere chiaramente la visualizzazione dei dati, il form di modifica e la logica server-side di aggiornamento.

## Visualizzazione profilo

Il file `profilo.php` mostra le informazioni principali dell’utente autenticato:

- nome utente;
- foto profilo;
- ruolo, cioè utente normale o admin;
- data di registrazione.

La pagina contiene anche il collegamento alla modifica del profilo. Se l’utente deve cambiare obbligatoriamente la password, il pulsante porta alla modifica password e l’accesso alle altre pagine viene bloccato finché il cambio non viene completato.

## Modifica profilo

Il file `modifica_profilo.php` contiene il form di modifica. Da questa pagina l’utente può aggiornare:

- nome utente;
- foto profilo;
- password.

Quando il cambio password è obbligatorio, ad esempio al primo accesso dell’admin di base, il sistema limita il form alla modifica della password. In questa situazione il nome utente è in sola lettura e il caricamento della foto profilo è disabilitato.

## Aggiornamento dati

Il file `update_profile.php` riceve i dati inviati dal form e aggiorna il database.

La modifica viene gestita lato server con controlli su:

- esistenza dell’utente in sessione;
- lunghezza minima del nome utente;
- unicità del nome utente;
- correttezza della password attuale quando viene cambiata la password;
- conferma della nuova password;
- differenza tra password vecchia e nuova;
- tipo MIME della foto profilo caricata.

Dopo un aggiornamento corretto vengono aggiornate anche le variabili di sessione, per mantenere coerenti nome utente e foto profilo mostrati nell’interfaccia.

## Foto profilo

La foto profilo è salvata nel campo `pfp` della tabella `utenti` come percorso relativo del file.

Il caricamento della foto profilo prevede:

- controllo del tipo MIME;
- accettazione dei formati immagine consentiti;
- generazione di un nome file univoco tramite `uniqid()`;
- salvataggio nella cartella `uploads/pfp/`;
- aggiornamento del percorso nel database.

Se l’utente non ha una foto profilo personale, viene utilizzata l’immagine predefinita definita nelle costanti applicative, ad esempio `DEFAULT_PFP`.

## Cambio password obbligatorio

Per gestire il primo accesso dell’admin di base è stato aggiunto il campo `must_change_password` nella tabella `utenti`.

Quando questo campo vale `1`, l’utente non può accedere alle pagine principali del sito e viene reindirizzato alla pagina profilo/modifica profilo.

Il flusso è il seguente:

1. l’utente effettua il login;
2. il sistema salva in sessione `must_change_password`;
3. se il valore è attivo, viene mostrato il profilo con avviso di cambio password richiesto;
4. l’utente accede al form di modifica;
5. dopo il cambio password, il database viene aggiornato impostando `must_change_password = 0`;
6. la sessione viene aggiornata e l’utente può accedere normalmente al sito.

## Utility di protezione

La protezione delle pagine viene centralizzata in un file utility, ad esempio:

```text
utils/auth_guard.php
```

Questo file contiene funzioni per:

- verificare che l’utente sia autenticato;
- verificare che l’utente sia admin;
- reindirizzare al profilo se è richiesto il cambio password.

In questo modo le pagine protette non devono duplicare sempre gli stessi controlli e il flusso di autenticazione resta più ordinato.
