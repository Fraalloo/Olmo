-- Link su luoghi attivi
INSERT INTO link_articoli(id_articolo, url_link)
SELECT id_articolo, 'https://it.wikipedia.org/wiki/Olmo'
FROM articoli
WHERE id_gruppo_articolo = 100
  AND versione = 3;

INSERT INTO link_articoli(id_articolo, url_link)
SELECT id_articolo, 'https://www.openstreetmap.org/'
FROM articoli
WHERE id_tipo_articolo = 1
  AND is_active = 1
  AND latitudine IS NOT NULL
  AND is_hidden = 0;

INSERT INTO link_articoli(id_articolo, url_link)
SELECT id_articolo, 'https://it.wikipedia.org/wiki/Piazza'
FROM articoli
WHERE id_gruppo_articolo = 109
  AND versione = 1;

-- Link su documenti attivi
INSERT INTO link_articoli(id_articolo, url_link)
SELECT id_articolo, 'https://it.wikipedia.org/wiki/Archivio'
FROM articoli
WHERE id_gruppo_articolo IN (101, 107, 111, 120, 125)
  AND is_active = 1;

INSERT INTO link_articoli(id_articolo, url_link)
SELECT id_articolo, 'https://it.wikipedia.org/wiki/Documento'
FROM articoli
WHERE id_tipo_articolo = 2
  AND is_active = 1
  AND is_hidden = 0;

-- Link su testimonianze
INSERT INTO link_articoli(id_articolo, url_link)
SELECT id_articolo, 'https://it.wikipedia.org/wiki/Testimonianza'
FROM articoli
WHERE id_tipo_articolo = 3
  AND is_active = 1
  AND is_hidden = 0;

INSERT INTO link_articoli(id_articolo, url_link)
SELECT id_articolo, 'https://it.wikipedia.org/wiki/Memoria'
FROM articoli
WHERE id_gruppo_articolo IN (102, 112, 118)
  AND is_active = 1;

-- Link su revisioni pendenti, per test della pagina convalida
INSERT INTO link_articoli(id_articolo, url_link)
SELECT id_articolo, 'https://example.com/proposta-aggiornamento'
FROM articoli
WHERE id_admin IS NULL
  AND is_active = 0;

-- Link su contenuti nascosti, per verificare che non compaiano in home pubblica
INSERT INTO link_articoli(id_articolo, url_link)
SELECT id_articolo, 'https://example.com/contenuto-nascosto'
FROM articoli
WHERE is_hidden = 1;
