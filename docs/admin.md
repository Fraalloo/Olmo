# Funzionalità Admin

## Descrizione generale

Nel progetto **Sotto l’Olmo** gli amministratori hanno privilegi superiori rispetto agli utenti normali. Un utente normale può proporre nuovi articoli e modifiche ad articoli esistenti, mentre l’admin ha il compito di convalidare, rifiutare, nascondere, ripristinare e gestire i contenuti pubblicati.

L’admin può inoltre accedere a una dashboard dedicata per consultare statistiche generali del sistema e gestire i ruoli degli utenti, compresa la promozione di utenti normali ad amministratori.

## Ruoli e autorizzazioni

Il ruolo dell’utente è determinato dal campo `is_admin` nella tabella `utenti`.

- `is_admin = 0`: utente normale.
- `is_admin = 1`: amministratore.

Gli utenti normali possono registrarsi autonomamente, ma non possono diventare admin tramite il form pubblico di registrazione. La promozione ad admin viene effettuata da un amministratore già esistente tramite dashboard.

L’utente amministratore iniziale è `DBAdmin`. Questo account non deve poter essere revocato, perché rappresenta l’admin di base del sistema.

## Primo accesso di DBAdmin

Per aumentare la sicurezza, nella tabella `utenti` è stato aggiunto il campo `must_change_password`.

Questo campo indica se l’utente deve cambiare obbligatoriamente la password prima di accedere alle altre sezioni del sito.

- `must_change_password = 1`: cambio password richiesto.
- `must_change_password = 0`: accesso normale consentito.

L’admin di base `DBAdmin` viene creato con `must_change_password = 1`, così al primo accesso viene reindirizzato alle pagine `profile.php` e `modifica_profile.php` per impostare una nuova password.

## Convalida dei contenuti

Gli articoli proposti dagli utenti e le modifiche ad articoli esistenti vengono salvati come versioni non approvate:

- `id_admin IS NULL`
- `is_active = 0`
- `is_hidden = 0`

Questi articoli non compaiono nella Home pubblica e vengono mostrati nella pagina `convalida.php`, accessibile solo agli admin.

Da questa pagina l’admin può:

- aprire il dettaglio dell’articolo;
- approvare/convalidare l’articolo;
- rifiutare l’articolo.

Se una proposta viene approvata, viene valorizzato `id_admin` con l’id dell’admin che ha eseguito l’azione e la versione diventa attiva. Se viene rifiutata, la proposta viene eliminata fisicamente dal database, perché non è mai entrata nello storico approvato.

## Gestione versioni e storico

Gli articoli sono raggruppati tramite `id_gruppo_articolo`. Ogni gruppo rappresenta un articolo logico, mentre le righe nella tabella `articoli` rappresentano le sue versioni.

La pagina di modifica crea una nuova versione nello stesso gruppo dell'articolo sorgente. La versione pubblicata resta visibile fino all'eventuale approvazione della modifica.

La versione attiva è indicata da `is_active = 1`. Una sola versione per gruppo deve essere considerata attiva.

Per gestire la rimozione logica è stato introdotto il campo `is_hidden`:

- `is_hidden = 0`: versione visibile o storica disponibile;
- `is_hidden = 1`: versione nascosta.

Una versione nascosta non viene mostrata agli utenti normali e non deve essere la versione attiva.

La regola applicativa è:

> la versione attiva deve essere la versione approvata, non hidden, con numero di versione più alto.

Questa regola viene gestita nel codice PHP tramite una funzione di ricalcolo, richiamata dopo approvazioni, eliminazioni logiche e ripristini.

## Eliminazione logica e ripristino

L’eliminazione di una versione approvata non cancella fisicamente la riga dal database. Invece imposta:

```sql
is_hidden = 1,
is_active = 0
```

Dopo l’eliminazione, il sistema ricalcola automaticamente quale versione del gruppo deve diventare attiva.

Il ripristino fa l’operazione inversa:

```sql
is_hidden = 0
```

Dopo il ripristino viene nuovamente ricalcolata la versione attiva. Se la versione ripristinata è la versione approvata non hidden più alta del gruppo, diventa attiva; altrimenti torna semplicemente disponibile nello storico.

## Popup e componenti admin

Le azioni admin nella pagina dettaglio articolo sono gestite dal componente:

```text
components/article_admin_actions/
```

Questo componente contiene:

- `article_admin_actions.php`: genera i pulsanti e la struttura HTML dei popup;
- `article_admin_actions.js`: intercetta i click, apre il popup corretto e invia il form dopo la conferma;
- `article_admin_actions.css`: definisce lo stile dei pulsanti e delle finestre modali.

Nella pagina `article.php` possono comparire azioni diverse in base allo stato della versione:

| Stato versione | Azioni mostrate |
|---|---|
| Non approvata | Convalida, Rifiuta |
| Approvata e non hidden | Elimina |
| Approvata e hidden | Ripristina |

La pagina `convalida.php` usa invece i popup collegati al componente della card articolo, perché opera su un elenco di proposte da approvare.

## Dashboard admin

La dashboard admin permette di consultare una vista sintetica dello stato del sistema e di gestire gli utenti.

Le statistiche previste includono:

- numero totale di utenti;
- numero di admin;
- numero di articoli attivi;
- numero di articoli in attesa di approvazione;
- numero di versioni hidden;
- numero totale di versioni/articoli.

La gestione utenti include:

- elenco utenti con paginazione;
- filtro per nome utente;
- filtro per ruolo;
- filtro per data di registrazione;
- filtro per sicurezza/cambio password richiesto;
- promozione di un utente ad admin;
- revoca del ruolo admin, escluso `DBAdmin` e l’utente attualmente loggato.

## Sicurezza delle azioni admin

Le azioni sensibili vengono eseguite tramite form `POST` e controllano sempre lato server che l’utente sia autenticato e amministratore.

Per maggiore sicurezza è consigliabile aggiungere anche token CSRF ai form di azione, in modo da evitare che richieste esterne possano sfruttare la sessione dell’admin per eseguire operazioni indesiderate.
