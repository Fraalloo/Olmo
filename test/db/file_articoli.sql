-- Allegato TXT condiviso: utile per contenuti semplici e testimonianze
INSERT INTO file_articoli(id_articolo, nome_originale, file_path, mime_type)
SELECT id_articolo,
       'memoria_locale.txt',
       'uploads/file/file_6a0ec3d0e93738.56743205.txt',
       'text/plain'
FROM articoli
WHERE id_gruppo_articolo IN (100, 102, 117, 118, 126)
  AND versione = 1;

-- PDF documentali
INSERT INTO file_articoli(id_articolo, nome_originale, file_path, mime_type)
SELECT id_articolo,
       'documento_memoria_locale.pdf',
       'uploads/file/file_6a0ec3d0e6a517.42018230.pdf',
       'application/pdf'
FROM articoli
WHERE id_gruppo_articolo = 101
  AND versione = 2;

INSERT INTO file_articoli(id_articolo, nome_originale, file_path, mime_type)
SELECT id_articolo,
       'archivio_1920.pdf',
       'uploads/file/file_6a0ec3d0e2b2b0.17603555.pdf',
       'application/pdf'
FROM articoli
WHERE id_gruppo_articolo = 111
  AND versione = 2;

INSERT INTO file_articoli(id_articolo, nome_originale, file_path, mime_type)
SELECT id_articolo,
       'registro_scolastico.pdf',
       'uploads/file/file_6a0ec3d10c2262.56818025.pdf',
       'application/pdf'
FROM articoli
WHERE id_gruppo_articolo = 120
  AND versione = 2;

INSERT INTO file_articoli(id_articolo, nome_originale, file_path, mime_type)
SELECT id_articolo,
       'mappe_catastali.pdf',
       'uploads/file/file_6a0ec3d0e8a5d4.75166835.pdf',
       'application/pdf'
FROM articoli
WHERE id_gruppo_articolo = 125
  AND versione = 1;

-- Immagini come allegati, per test anteprime/download e MIME diversi
INSERT INTO file_articoli(id_articolo, nome_originale, file_path, mime_type)
SELECT id_articolo,
       'fontana_storica.jpg',
       'uploads/file/file_6a0ec3d0e75cc6.25253764.jpg',
       'image/jpeg'
FROM articoli
WHERE id_gruppo_articolo = 103
  AND versione = 1;

INSERT INTO file_articoli(id_articolo, nome_originale, file_path, mime_type)
SELECT id_articolo,
       'archivio_fotografico_01.png',
       'uploads/file/file_6a0ec3d0e3eda1.88611181.png',
       'image/png'
FROM articoli
WHERE id_gruppo_articolo = 104
  AND versione = 1;

INSERT INTO file_articoli(id_articolo, nome_originale, file_path, mime_type)
SELECT id_articolo,
       'archivio_fotografico_02.webp',
       'uploads/file/file_6a0ec3d0e4ad59.32722142.webp',
       'image/webp'
FROM articoli
WHERE id_gruppo_articolo = 104
  AND versione = 1;

INSERT INTO file_articoli(id_articolo, nome_originale, file_path, mime_type)
SELECT id_articolo,
       'mappa_catastale_estratto.png',
       'uploads/file/file_6a0ec3d0e7e746.49987730.png',
       'image/png'
FROM articoli
WHERE id_gruppo_articolo = 125
  AND versione = 1;

-- Allegati su revisioni pendenti, utili per convalida e rifiuto
INSERT INTO file_articoli(id_articolo, nome_originale, file_path, mime_type)
SELECT id_articolo,
       'proposta_aggiornamento.txt',
       'uploads/file/file_6a0ec3d0ebb433.01508515.txt',
       'text/plain'
FROM articoli
WHERE (id_gruppo_articolo = 100 AND versione = 4)
   OR (id_gruppo_articolo = 111 AND versione = 3)
   OR (id_gruppo_articolo = 122 AND versione = 2);
