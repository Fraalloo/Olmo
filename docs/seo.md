# SEO

Questo documento descrive le funzionalità aggiunte per migliorare indicizzazione,
condivisione e leggibilità esterna delle pagine pubbliche del progetto.

## sitemap.php

Il file `sitemap.php` si trova nella root del progetto e genera una sitemap XML dinamica.

La sitemap contiene:

- homepage;
- pagine informative in `src/pages/others`;
- articoli attivi, approvati e non nascosti.

Gli articoli vengono estratti dal database usando questi criteri:

- `is_active = 1`;
- `is_hidden = 0`;
- `id_admin IS NOT NULL`.

In questo modo la sitemap esclude revisioni pendenti, versioni storiche non attive
e contenuti nascosti.

La sitemap può essere aperta dal server web con:

```text
http://localhost/progetto-inf/sitemap.php
```

## Tag Meta E Open Graph

La pagina `src/pages/article/article.php` genera tag meta dinamici per ogni articolo.

I tag principali sono:

- `description`, derivato dalla descrizione dell'articolo;
- `author`, derivato dall'autore dell'articolo;
- `og:type`;
- `og:locale`;
- `og:site_name`;
- `og:title`;
- `og:description`;
- `og:url`;
- `og:image`, presente solo se l'articolo ha un banner.

Questi tag aiutano motori di ricerca, crawler e applicazioni esterne a descrivere
correttamente la pagina. I tag Open Graph sono usati anche per generare anteprime
quando un link viene condiviso su piattaforme compatibili.

## robots.txt

Il file `robots.txt` si trova nella root del progetto e fornisce indicazioni ai crawler.

Il file:

- consente la scansione generale del sito;
- blocca aree operative o non pubbliche;
- indica la posizione della sitemap.

Le directory bloccate includono:

- `src/auth`;
- `src/pages/admin`;
- `src/pages/convalida`;
- `src/pages/edit`;
- `src/pages/insert`;
- `scripts`;
- `routine`;
- `test`;
- `db`.
