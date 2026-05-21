# Timeout della sessione

## Descrizione generale

Il progetto applica un timeout di inattività alle sessioni autenticate.

La durata configurata è di 30 minuti:

```php
$timeout_seconds = 1800;
```

La logica si trova in:

```text
src/utils/auth_guard.php
```

## Funzionamento

Il controllo è eseguito dalla funzione `check_session_timeout()`, richiamata automaticamente da `require_login()`.

A ogni richiesta autenticata:

1. viene verificata la presenza di `$_SESSION["user_id"]`;
2. viene confrontato l'orario corrente con `$_SESSION["last_activity"]`;
3. se sono passati più di 30 minuti, la sessione viene svuotata e distrutta;
4. l'utente viene reindirizzato alla pagina di accesso;
5. se la sessione è ancora valida, `last_activity` viene aggiornato.

## Login

Al login riuscito, `src/auth/login.php` inizializza:

```php
$_SESSION["last_activity"] = time();
```

In questo modo il conteggio dell'inattività parte dal momento dell'accesso.

## Messaggio all'utente

Quando la sessione scade, l'utente viene reindirizzato a:

```text
src/auth/access.php?mode=login&timeout=1
```

`access.php` mostra il messaggio:

```text
Sessione scaduta per inattività. Effettua di nuovo l'accesso.
```

Il messaggio viene passato tramite query string per restare disponibile anche dopo la distruzione della sessione.

## Note operative

Il timeout non elimina dati dal database e non modifica l'utente.

Le pagine non protette non applicano questo controllo. Per rendere una nuova pagina soggetta al timeout è sufficiente avviare la sessione e richiamare `require_login()`.
