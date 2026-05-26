# GitHub Workflows

## Descrizione Generale

La cartella:

```text
.github/workflows/
```

contiene i workflow GitHub Actions del progetto.

La cartella:

```text
.github/config/
```

contiene file di configurazione usati dai workflow.

I workflow vengono eseguiti da GitHub in una macchina virtuale separata dal server locale XAMPP. Servono per automatizzare controlli sul codice prima o dopo push e pull request.

I push diretti su `main` non vengono bloccati preventivamente dai workflow. I controlli vengono eseguiti dopo il push e segnalano l'esito su GitHub.

Se le modifiche arrivano tramite pull request, il merge su `main` viene bloccato dalla branch protection rule finché i controlli richiesti non vengono superati.

## php-check.yml

`php-check.yml` controlla la sintassi dei file PHP del progetto.

### - File Coinvolti

```text
.github/workflows/php-check.yml
```

### - Eventi Di Esecuzione

Il workflow viene eseguito su:

- `push`;
- `pull_request`.

### - Job

Il workflow contiene il job:

```text
php-lint
```

Il job viene mostrato su GitHub con il nome:

```text
PHP Syntax
```

### - Step

Il workflow:

1. scarica il repository con `actions/checkout@v4`;
2. installa PHP con `shivammathur/setup-php@v2`;
3. abilita l'estensione `mysqli`;
4. esegue il lint dei file PHP.

Il comando principale è:

```bash
find . \
  -path "./uploads" -prune -o \
  -path "./test/uploads" -prune -o \
  -name "*.php" -print0 \
  | xargs -0 -n 1 php -l
```

### - Controlli Eseguiti

`php -l` controlla la sintassi PHP senza eseguire l'applicazione.

Il workflow intercetta errori come:

- parentesi mancanti;
- punti e virgola mancanti;
- errori sintattici bloccanti;
- file PHP non parsabili.

### - Protezione Del Branch Main

Il workflow è inserito nella branch protection rule di `main` per il controllo delle pull request.

La rule richiede il superamento del controllo:

```text
PHP Syntax
```

## js-check.yml

`js-check.yml` controlla la sintassi dei file JavaScript del progetto.

### - File Coinvolti

```text
.github/workflows/js-check.yml
```

### - Eventi Di Esecuzione

Il workflow viene eseguito su:

- `push`;
- `pull_request`.

### - Job

Il workflow contiene il job:

```text
js-lint
```

Il job viene mostrato su GitHub con il nome:

```text
JavaScript Syntax
```

### - Step

Il workflow:

1. scarica il repository con `actions/checkout@v4`;
2. installa Node.js con `actions/setup-node@v4`;
3. esegue il controllo sintattico dei file JavaScript.

Il comando principale è:

```bash
find . \
  -path "./uploads" -prune -o \
  -path "./test/uploads" -prune -o \
  -name "*.js" -print0 \
  | xargs -0 -n 1 node --check
```

### - Controlli Eseguiti

`node --check` controlla la sintassi JavaScript senza eseguire realmente il codice.

Il workflow intercetta errori come:

- parentesi mancanti;
- import o statement scritti male;
- errori sintattici bloccanti;
- file JavaScript non parsabili.

### - Protezione Del Branch Main

Il workflow è inserito nella branch protection rule di `main` per il controllo delle pull request.

La rule richiede il superamento del controllo:

```text
JavaScript Syntax
```

## db-check.yml

`db-check.yml` controlla che lo schema SQL e i record di test siano importabili in un database MariaDB temporaneo.

### - File Coinvolti

```text
.github/workflows/db-check.yml
```

Il workflow importa:

```text
db/db_schema.sql
test/db/utenti.sql
test/db/gruppi_articoli.sql
test/db/articoli.sql
test/db/file_articoli.sql
test/db/link_articoli.sql
```

### - Eventi Di Esecuzione

Il workflow viene eseguito su:

- `push`;
- `pull_request`.

### - Job

Il workflow contiene il job:

```text
db-import
```

Il job viene mostrato su GitHub con il nome:

```text
Database Import
```

### - Servizio MariaDB

Il workflow avvia un servizio MariaDB temporaneo:

```text
mariadb:11
```

Il servizio viene usato solo durante l'esecuzione del workflow e viene eliminato al termine del job.

### - Step

Il workflow:

1. scarica il repository con `actions/checkout@v4`;
2. installa il client MariaDB;
3. importa `db/db_schema.sql`;
4. importa i record di test nello stesso ordine dello script locale;
5. esegue una verifica con `SELECT COUNT(*)` sulle tabelle principali.

### - Controlli Eseguiti

Il workflow controlla che:

- `db_schema.sql` sia importabile;
- il database `Olmo` venga creato correttamente;
- le tabelle principali vengano create;
- i record di test rispettino vincoli, foreign key e ordine di import;
- i dati vengano effettivamente inseriti nelle tabelle principali.

### - Protezione Del Branch Main

Il workflow è inserito nella branch protection rule di `main` per il controllo delle pull request.

La rule richiede il superamento del controllo:

```text
Database Import
```

## md-check.yml

`md-check.yml` controlla la formattazione dei file Markdown del progetto.

### - File Coinvolti

```text
.github/workflows/md-check.yml
.github/config/.markdownlint-cli2.jsonc
```

Il workflow controlla:

```text
ReadME.md
docs/**/*.md
```

### - Eventi Di Esecuzione

Il workflow viene eseguito su:

- `push`;
- `pull_request`.

### - Job

Il workflow contiene il job:

```text
markdown-lint
```

Il job viene mostrato su GitHub con il nome:

```text
Markdown Lint
```

### - Step

Il workflow:

1. scarica il repository con `actions/checkout@v4`;
2. installa Node.js con `actions/setup-node@v4`;
3. installa `markdownlint-cli2` con `npm`;
4. esegue `markdownlint-cli2`.

Il comando principale è:

```bash
markdownlint-cli2 \
  --config ".github/config/.markdownlint-cli2.jsonc" \
  "ReadME.md" \
  "docs/**/*.md"
```

### - Controlli Eseguiti

Il workflow controlla la struttura Markdown dei file di documentazione.

La configurazione è contenuta in:

```text
.github/config/.markdownlint-cli2.jsonc
```

Attualmente sono disattivate alcune regole troppo invasive per la documentazione esistente:

- `MD013`, lunghezza massima delle righe;
- `MD024`, titoli duplicati in sezioni diverse;
- `MD033`, HTML inline.

### - Protezione Del Branch Main

Il workflow è inserito nella branch protection rule di `main` per il controllo delle pull request.

La rule richiede il superamento del controllo:

```text
Markdown Lint
```
