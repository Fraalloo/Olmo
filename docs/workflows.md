# GitHub Workflows

## Descrizione Generale

La cartella:

```text
.github/workflows/
```

contiene i workflow GitHub Actions del progetto.

I workflow vengono eseguiti da GitHub in una macchina virtuale separata dal server locale XAMPP. Servono per automatizzare controlli sul codice prima o dopo push e pull request.

## php-check.yml

`php-check.yml` controlla la sintassi dei file PHP del progetto.

### - File Coinvolti

Il file del workflow è:

```text
.github/workflows/php-check.yml
```

### - Eventi Di Esecuzione

Il workflow viene eseguito su:

- `push`;
- `pull_request`.

Questo significa che GitHub avvia il controllo quando viene inviato codice al repository o quando viene aperta/aggiornata una pull request.

### - Job

Il workflow contiene un job:

```text
php-lint
```

Il job viene mostrato su GitHub con il nome:

```text
PHP Syntax
```

Il job gira su:

```text
ubuntu-latest
```

quindi su una macchina Linux gestita da GitHub.

### - Step

Il workflow contiene tre step principali.

Il primo step usa:

```text
actions/checkout@v4
```

Serve a scaricare il codice del repository nella macchina virtuale del workflow.

Il secondo step usa:

```text
shivammathur/setup-php@v2
```

Serve a installare PHP nel runner GitHub.

La configurazione usata è:

```text
php-version: 8.2
extensions: mysqli
coverage: none
```

Il terzo step esegue il lint dei file PHP:

```bash
find . \
  -path "./uploads" -prune -o \
  -path "./test/uploads" -prune -o \
  -name "*.php" -print0 \
  | xargs -0 -n 1 php -l
```

### - Controlli Eseguiti

Il comando cerca tutti i file `.php` del repository, escludendo:

```text
uploads/
test/uploads/
```

Per ogni file esegue:

```bash
php -l
```

`php -l` controlla la sintassi PHP senza eseguire l'applicazione.

Il workflow può intercettare errori come:

- parentesi mancanti;
- punti e virgola mancanti;
- errori sintattici bloccanti;
- file PHP non parsabili.

### - Risultato Del Workflow

Se il workflow passa, GitHub mostra il controllo in verde.

Se il workflow fallisce:

- il push non viene annullato;
- il commit resta nel repository;
- GitHub mostra il controllo in rosso;
- sarà necessario correggere l'errore con un nuovo commit.

Il merge su `main` viene bloccato solo se nel repository sono configurate branch protection rules che richiedono il superamento del workflow.

### - Protezione Del Branch Main

Attualmente il workflow non è collegato a una branch protection rule obbligatoria.