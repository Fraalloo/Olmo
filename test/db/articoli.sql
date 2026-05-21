INSERT INTO articoli(
    id_gruppo_articolo,
    id_tipo_articolo,
    id_pubblicatore,
    id_admin,
    banner,
    titolo,
    descrizione,
    latitudine,
    longitudine,
    data_pubblicazione,
    versione,
    is_active,
    is_hidden
) VALUES

-- Gruppo 100: luogo con storico, versione attiva e revisione pendente
(100,1,100,1,NULL,'Olmo storico del paese','Prima versione del luogo, mantenuta nello storico.',41.702100,15.722500,'2024-01-10',1,0,0),
(100,1,100,1,NULL,'Olmo storico del paese','Seconda versione approvata ma non più attiva.',41.703500,15.724200,'2024-03-15',2,0,0),
(100,1,100,1,'uploads/banner/banner_6a0ec3d11b21c0.92238768.jpg','Olmo storico del paese','Versione attiva con coordinate, banner, link e allegati.',41.705000,15.726000,'2024-06-01',3,1,0),
(100,1,101,NULL,'uploads/banner/banner_6a0ec48a563d67.98954733.jpg','Olmo storico del paese','Aggiornamento proposto in attesa di convalida.',41.706800,15.728800,'2024-09-20',4,0,0),

-- Gruppo 101: documento senza coordinate, attivo e revisione pendente
(101,2,101,1,NULL,'Documento memoria locale','Prima bozza del documento storico.',NULL,NULL,'2023-11-05',1,0,0),
(101,2,101,1,'uploads/banner/banner_6a0ec3d116c7a9.75205312.jpg','Documento memoria locale','Versione attiva senza coordinate, con PDF allegato.',NULL,NULL,'2024-02-18',2,1,0),
(101,2,102,NULL,NULL,'Documento memoria locale','Revisione proposta senza banner.',NULL,NULL,'2024-07-10',3,0,0),

-- Gruppo 102: testimonianza senza coordinate
(102,3,102,1,NULL,'Testimonianza abitante','Prima trascrizione della testimonianza.',NULL,NULL,'2024-01-25',1,0,0),
(102,3,102,1,'uploads/banner/banner_6a0ec3d115fea4.79276160.jpg','Testimonianza abitante','Versione attiva con file audio trascritto in TXT.',NULL,NULL,'2024-04-12',2,1,0),
(102,3,103,NULL,NULL,'Testimonianza abitante','Aggiornamento proposto da un altro utente.',NULL,NULL,'2024-08-03',3,0,0),

-- Gruppi 103-105: casi semplici attivi per ogni tipo
(103,1,103,1,'uploads/banner/banner_6a0ec3d1188c07.18015863.jpg','Antica fontana','Luogo attivo con coordinate e immagine allegata.',41.709200,15.733100,'2023-12-01',1,1,0),
(104,2,104,1,NULL,'Archivio fotografico','Documento attivo con più allegati immagine.',NULL,NULL,'2024-02-01',1,1,0),
(105,3,105,1,NULL,'Ricordo scuola','Testimonianza attiva senza link e senza allegati.',NULL,NULL,'2024-03-01',1,1,0),

-- Gruppo 106: luogo attivo più aggiornamento pendente
(106,1,100,1,'uploads/banner/banner_6a0ec3d11dbc62.45263167.jpg','Vecchio sentiero','Percorso attivo con coordinate.',41.712000,15.735500,'2024-01-10',1,1,0),
(106,1,101,NULL,NULL,'Vecchio sentiero','Aggiornamento pendente con coordinate diverse.',41.713500,15.737200,'2024-05-22',2,0,0),

-- Gruppi 107-109: contenuti attivi utili per filtri e mappa
(107,2,101,1,'uploads/banner/banner_6a0ec3d11ccf67.92898966.jpg','Registro comunale','Documento attivo con link esterno.',NULL,NULL,'2024-01-05',1,1,0),
(108,3,102,1,NULL,'Racconto famiglia','Testimonianza approvata ma non attiva, utile come storico non visibile in home.',NULL,NULL,'2024-02-10',1,0,0),
(109,1,103,1,'uploads/banner/banner_6a0ec3d11bf6f1.09854615.jpg','Piazza centrale','Luogo attivo con coordinate e link multipli.',41.707800,15.729900,'2024-03-20',1,1,0),

-- Gruppi 110-120: dataset più ampio per paginazione, filtri e versioni
(110,1,100,1,NULL,'Bosco antico','Prima versione storica del luogo.',41.701500,15.721000,'2023-10-10',1,0,0),
(110,1,100,1,'uploads/banner/banner_6a0ec48a59bc86.50965723.jpg','Bosco antico','Versione attiva con coordinate.',41.702800,15.722800,'2024-02-15',2,1,0),
(111,2,101,1,NULL,'Archivio 1920','Prima versione del documento.',NULL,NULL,'2023-09-01',1,0,0),
(111,2,101,1,'uploads/banner/banner_6a0ec3d1151df3.77528514.jpg','Archivio 1920','Versione attiva con PDF.',NULL,NULL,'2024-01-20',2,1,0),
(111,2,102,NULL,NULL,'Archivio 1920','Revisione pendente con nuovo allegato.',NULL,NULL,'2024-06-18',3,0,0),
(112,3,102,1,NULL,'Ricordi di guerra','Testimonianza attiva con link a fonte orale.',NULL,NULL,'2024-03-03',1,1,0),
(113,1,103,1,NULL,'Vecchio mulino','Luogo attivo senza banner.',41.714500,15.739000,'2024-01-11',1,1,0),
(113,1,104,NULL,NULL,'Vecchio mulino','Aggiornamento pendente.',41.715000,15.740000,'2024-07-01',2,0,0),
(114,2,104,1,NULL,'Lettere storiche','Documento attivo senza allegati.',NULL,NULL,'2023-12-12',1,1,0),
(115,3,105,1,NULL,'Infanzia dopoguerra','Prima versione storica.',NULL,NULL,'2024-01-05',1,0,0),
(115,3,105,1,NULL,'Infanzia dopoguerra','Versione attiva senza banner e senza coordinate.',NULL,NULL,'2024-04-10',2,1,0),
(116,1,100,1,NULL,'Chiesa abbandonata','Luogo attivo senza banner ma con coordinate.',41.700500,15.720500,'2024-02-14',1,1,0),
(117,2,101,1,NULL,'Statuto comunale','Documento attivo con file TXT.',NULL,NULL,'2023-11-30',1,1,0),
(117,2,102,NULL,NULL,'Statuto comunale','Aggiornamento pendente.',NULL,NULL,'2024-08-02',2,0,0),
(118,3,102,1,NULL,'Memorie contadine','Testimonianza attiva con allegato TXT.',NULL,NULL,'2024-03-20',1,1,0),
(119,1,103,1,NULL,'Ponte antico','Luogo attivo con coordinate e nessun file.',41.708500,15.731000,'2024-01-25',1,1,0),
(120,2,104,1,NULL,'Registro scolastico','Prima versione storica.',NULL,NULL,'2023-10-01',1,0,0),
(120,2,104,1,NULL,'Registro scolastico','Versione attiva con PDF e link.',NULL,NULL,'2024-02-01',2,1,0),
(120,2,105,NULL,NULL,'Registro scolastico','Revisione pendente.',NULL,NULL,'2024-06-30',3,0,0),

-- Gruppo 121: articolo attivo nascosto, utile per azioni admin ripristino
(121,1,106,1,'uploads/banner/banner_6a0ec3d11a4a59.56296281.jpg','Belvedere nascosto','Luogo attivo ma nascosto dagli admin.',41.716200,15.742000,'2024-04-02',1,1,1),

-- Gruppo 122: revisione pendente di articolo nascosto
(122,2,107,1,'uploads/banner/banner_6a0ec3d117aee5.68972877.jpg','Fascicolo riservato','Documento attivo ma nascosto.',NULL,NULL,'2024-03-22',1,1,1),
(122,2,108,NULL,NULL,'Fascicolo riservato','Revisione pendente di contenuto nascosto.',NULL,NULL,'2024-09-01',2,0,1),

-- Gruppo 123: luogo attivo senza coordinate, utile per filtro "senza coordinate"
(123,1,109,1,'uploads/banner/banner_6a0ec56b278224.36584418.jpg','Casa rurale non localizzata','Luogo approvato senza coordinate.',NULL,NULL,'2024-05-09',1,1,0),

-- Gruppo 124: luogo pendente senza coordinate
(124,1,110,NULL,NULL,'Croce viaria','Nuovo luogo proposto senza coordinate.',NULL,NULL,'2024-09-11',1,0,0),

-- Gruppo 125: documento attivo con immagini e PDF
(125,2,111,1,'uploads/banner/banner_6a0ec3d11966f9.82219796.jpg','Mappe catastali','Documento attivo con allegati misti.',NULL,NULL,'2024-05-15',1,1,0),

-- Gruppo 126: testimonianza pendente senza admin
(126,3,112,NULL,NULL,'Racconto del mercato','Testimonianza nuova in attesa di approvazione.',NULL,NULL,'2024-09-14',1,0,0),

-- Gruppo 127: luogo con coordinate al limite valido
(127,1,113,1,NULL,'Coordinate limite nord','Luogo tecnico per test latitudine massima.',90.000000,15.000000,'2024-05-25',1,1,0),

-- Gruppo 128: luogo con coordinate al limite valido negativo
(128,1,114,1,NULL,'Coordinate limite sud ovest','Luogo tecnico per test coordinate negative.',-90.000000,-180.000000,'2024-05-26',1,1,0),

-- Gruppo 129: testimonianza attiva nascosta
(129,3,115,1,NULL,'Testimonianza da revisionare','Testimonianza approvata ma nascosta.',NULL,NULL,'2024-06-01',1,1,1);
