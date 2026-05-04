INSERT INTO file_articoli(id_articolo, nome_originale, file_path, mime_type)
SELECT id_articolo,
       'file_69e721314c0394.02084360.txt',
       'uploads/file/file_69e721314c0394.02084360.txt',
       'text/plain'
FROM articoli
WHERE id_gruppo_articolo BETWEEN 100 AND 109;