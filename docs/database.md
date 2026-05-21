# Documentazione Del Database

## Descrizione Generale

Il database `Olmo` memorizza utenti, contenuti pubblicati, versioni degli articoli, file allegati e link di approfondimento.

Le tipologie di articolo previste sono:

- `luogo`
- `documento`
- `testimonianza`

Gli articoli usano un sistema di versioning: `gruppi_articoli` rappresenta l'articolo logico, mentre `articoli` contiene le singole versioni.

## Tabelle

### utenti

Contiene gli account registrati.

| Campo | Tipo | Vincoli | Descrizione |
|---|---|---|---|
| `id_utente` | `INT` | PK, AUTO_INCREMENT | Identificativo utente |
| `nome_utente` | `VARCHAR(50)` | UNIQUE, `LENGTH(nome_utente) > 3` | Nome utente |
| `password_hash` | `VARCHAR(255)` | NOT NULL | Hash della password |
| `pfp` | `VARCHAR(255)` | NULL | Percorso relativo della foto profilo |
| `data_registrazione` | `DATE` | DEFAULT `CURRENT_DATE` | Data registrazione |
| `is_admin` | `BOOLEAN` | DEFAULT `0`, CHECK booleano | Ruolo amministratore |
| `must_change_password` | `BOOLEAN` | DEFAULT `0`, CHECK booleano | Cambio password obbligatorio |

La registrazione pubblica crea solo utenti normali. Gli admin vengono promossi da altri admin o creati dal DBA.

### tipi_articoli

Elenca le categorie disponibili.

| Campo | Tipo | Vincoli | Descrizione |
|---|---|---|---|
| `id_tipo_articolo` | `INT` | PK, AUTO_INCREMENT | Identificativo tipo |
| `descrizione` | `VARCHAR(20)` | UNIQUE | `luogo`, `documento`, `testimonianza` |

### gruppi_articoli

Rappresenta un articolo logico, indipendente dalle sue versioni.

| Campo | Tipo | Vincoli | Descrizione |
|---|---|---|---|
| `id_gruppo_articolo` | `INT` | PK, AUTO_INCREMENT | Identificativo gruppo |
| `data_creazione` | `DATE` | DEFAULT `CURRENT_DATE` | Data creazione gruppo |

### articoli

Contiene le versioni degli articoli.

| Campo | Tipo | Vincoli | Descrizione |
|---|---|---|---|
| `id_articolo` | `INT` | PK, AUTO_INCREMENT | Identificativo versione |
| `id_gruppo_articolo` | `INT` | FK, UNIQUE con `versione` | Gruppo dell'articolo logico |
| `id_tipo_articolo` | `INT` | FK, indice | Tipo articolo |
| `id_pubblicatore` | `INT` | FK, indice | Utente autore della versione |
| `id_admin` | `INT` | FK NULL, indice | Admin che ha approvato la versione |
| `banner` | `VARCHAR(255)` | NULL | Percorso relativo del banner |
| `titolo` | `VARCHAR(100)` | NOT NULL | Titolo |
| `descrizione` | `TEXT` | NOT NULL | Contenuto testuale |
| `latitudine` | `DECIMAL(9,6)` | NULL, CHECK `-90..90` | Coordinata opzionale |
| `longitudine` | `DECIMAL(9,6)` | NULL, CHECK `-180..180` | Coordinata opzionale |
| `data_pubblicazione` | `DATE` | DEFAULT `CURRENT_DATE`, indice | Data versione |
| `versione` | `INT` | DEFAULT `1`, CHECK `>= 1` | Numero versione nel gruppo |
| `is_active` | `BOOLEAN` | DEFAULT `0`, indice | Versione attiva |
| `is_hidden` | `BOOLEAN` | DEFAULT `0`, indice | Versione nascosta |

Una proposta non ancora approvata ha normalmente:

```sql
id_admin IS NULL
is_active = 0
is_hidden = 0
```

La versione visibile in Home è approvata, attiva e non nascosta:

```sql
id_admin IS NOT NULL
is_active = 1
is_hidden = 0
```

### file_articoli

Contiene i file allegati agli articoli.

| Campo | Tipo | Vincoli | Descrizione |
|---|---|---|---|
| `id_file` | `INT` | PK, AUTO_INCREMENT | Identificativo file |
| `id_articolo` | `INT` | FK, indice | Versione articolo collegata |
| `nome_originale` | `VARCHAR(255)` | NOT NULL | Nome file caricato dall'utente |
| `file_path` | `VARCHAR(255)` | NOT NULL | Percorso relativo del file salvato |
| `mime_type` | `VARCHAR(100)` | NOT NULL | MIME rilevato lato server |
| `data_upload` | `DATE` | DEFAULT `CURRENT_DATE` | Data caricamento |

Il file fisico viene salvato con un nome generato tramite `uniqid()`, mentre `nome_originale` conserva il nome scelto dall'utente.

### link_articoli

Contiene direttamente i link associati agli articoli. La vecchia tabella separata `link` è stata rimossa.

| Campo | Tipo | Vincoli | Descrizione |
|---|---|---|---|
| `id_link_articolo` | `INT` | PK, AUTO_INCREMENT | Identificativo link |
| `id_articolo` | `INT` | FK, indice | Versione articolo collegata |
| `url_link` | `VARCHAR(255)` | NOT NULL | URL di approfondimento |

## Relazioni

- `articoli.id_gruppo_articolo` -> `gruppi_articoli.id_gruppo_articolo`
- `articoli.id_tipo_articolo` -> `tipi_articoli.id_tipo_articolo`
- `articoli.id_pubblicatore` -> `utenti.id_utente`
- `articoli.id_admin` -> `utenti.id_utente`
- `file_articoli.id_articolo` -> `articoli.id_articolo`
- `link_articoli.id_articolo` -> `articoli.id_articolo`

## DDL Attuale

La definizione fisica aggiornata è contenuta in:

```text
db/db_schema.sql
```

Questo file crea il database, le tabelle, gli indici, i vincoli e i dati iniziali minimi:

- tipi articolo;
- admin iniziale `DBAdmin` con `id_utente = 1`;
- `must_change_password = 1` per forzare il cambio password al primo accesso.

## Dati Di Test

La cartella:

```text
test/db/
```

contiene record di esempio importabili manualmente oppure tramite lo script di inizializzazione. I record coprono:

- utenti normali e admin;
- articoli attivi, storici, pendenti e hidden;
- contenuti con e senza coordinate;
- banner, allegati e link;
- file TXT, PDF, JPG, PNG e WEBP.

I file fisici usati dai record si trovano in:

```text
test/uploads/
```

Per usarli nell'applicazione, la cartella `test/uploads` deve essere copiata o spostata nella root del progetto come `uploads`.