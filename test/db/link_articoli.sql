INSERT INTO link_articoli(id_articolo, url_link)
SELECT id_articolo, 'https://it.wikipedia.org/wiki/Olmo'
FROM articoli
WHERE id_gruppo_articolo IN (100, 103, 106);

INSERT INTO link_articoli(id_articolo, url_link)
SELECT id_articolo, 'https://it.wikipedia.org/wiki/Memoria'
FROM articoli
WHERE id_gruppo_articolo IN (101, 104, 107);

INSERT INTO link_articoli(id_articolo, url_link)
SELECT id_articolo, 'https://it.wikipedia.org/wiki/Testimonianza'
FROM articoli
WHERE id_gruppo_articolo IN (102, 105, 108);

INSERT INTO link_articoli(id_articolo, url_link)
SELECT id_articolo, 'https://it.wikipedia.org/wiki/Piazza'
FROM articoli
WHERE id_gruppo_articolo = 109;