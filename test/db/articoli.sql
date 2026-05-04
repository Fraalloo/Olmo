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
    is_active
) VALUES

-- Gruppo 100
(100,1,100,1,NULL,'Olmo storico del paese','Prima versione.',41.7021,15.7225,'2024-01-10',1,0),
(100,1,100,1,NULL,'Olmo storico del paese','Seconda versione.',41.7035,15.7242,'2024-03-15',2,0),
(100,1,100,1,'uploads/banner/banner_69e721314c0394.02084360.png','Olmo storico del paese','Versione attiva.',41.7050,15.7260,'2024-06-01',3,1),
(100,1,101,NULL,'uploads/banner/banner_69e721314c0394.02084360.png','Olmo storico del paese','Aggiornamento proposto.',41.7068,15.7288,'2024-09-20',4,0),

-- Gruppo 101
(101,2,101,1,NULL,'Documento memoria locale','Prima bozza.',NULL,NULL,'2023-11-05',1,0),
(101,2,101,1,'uploads/banner/banner_69e721314c0394.02084360.png','Documento memoria locale','Versione attiva.',NULL,NULL,'2024-02-18',2,1),
(101,2,102,NULL,NULL,'Documento memoria locale','Revisione proposta.',NULL,NULL,'2024-07-10',3,0),

-- Gruppo 102
(102,3,102,1,NULL,'Testimonianza abitante','Prima versione.',NULL,NULL,'2024-01-25',1,0),
(102,3,102,1,'uploads/banner/banner_69e721314c0394.02084360.png','Testimonianza abitante','Versione attiva.',NULL,NULL,'2024-04-12',2,1),
(102,3,103,NULL,NULL,'Testimonianza abitante','Aggiornamento.',NULL,NULL,'2024-08-03',3,0),

-- Gruppo 103
(103,1,103,1,'uploads/banner/banner_69e721314c0394.02084360.png','Antica fontana','Luogo storico.',41.7092,15.7331,'2023-12-01',1,1),

-- Gruppo 104
(104,2,104,1,NULL,'Archivio fotografico','Documento.',NULL,NULL,'2024-02-01',1,1),

-- Gruppo 105
(105,3,105,1,NULL,'Ricordo scuola','Testimonianza.',NULL,NULL,'2024-03-01',1,1),

-- Gruppo 106
(106,1,100,1,'uploads/banner/banner_69e721314c0394.02084360.png','Vecchio sentiero','Percorso.',41.7120,15.7355,'2024-01-10',1,1),
(106,1,101,NULL,NULL,'Vecchio sentiero','Aggiornamento.',41.7135,15.7372,'2024-05-22',2,0),

-- Gruppo 107
(107,2,101,1,'uploads/banner/banner_69e721314c0394.02084360.png','Registro comunale','Documento.',NULL,NULL,'2024-01-05',1,1),

-- Gruppo 108
(108,3,102,1,NULL,'Racconto famiglia','Testimonianza.',NULL,NULL,'2024-02-10',1,0),

-- Gruppo 109
(109,1,103,1,'uploads/banner/banner_69e721314c0394.02084360.png','Piazza centrale','Centro.',41.7078,15.7299,'2024-03-20',1,1),

-- Gruppo 110
(110,1,100,1,NULL,'Bosco antico','Prima versione.',41.7015,15.7210,'2023-10-10',1,0),
(110,1,100,1,'uploads/banner/banner_69e721314c0394.02084360.png','Bosco antico','Versione attiva.',41.7028,15.7228,'2024-02-15',2,1),

-- Gruppo 111
(111,2,101,1,NULL,'Archivio 1920','Prima versione.',NULL,NULL,'2023-09-01',1,0),
(111,2,101,1,'uploads/banner/banner_69e721314c0394.02084360.png','Archivio 1920','Versione attiva.',NULL,NULL,'2024-01-20',2,1),
(111,2,102,NULL,NULL,'Archivio 1920','Revisione.',NULL,NULL,'2024-06-18',3,0),

-- Gruppo 112
(112,3,102,1,NULL,'Ricordi di guerra','Testimonianza.',NULL,NULL,'2024-03-03',1,1),

-- Gruppo 113
(113,1,103,1,NULL,'Vecchio mulino','Luogo.',41.7145,15.7390,'2024-01-11',1,1),
(113,1,104,NULL,NULL,'Vecchio mulino','Aggiornamento.',41.7150,15.7400,'2024-07-01',2,0),

-- Gruppo 114
(114,2,104,1,NULL,'Lettere storiche','Documento.',NULL,NULL,'2023-12-12',1,1),

-- Gruppo 115
(115,3,105,1,NULL,'Infanzia dopoguerra','Prima versione.',NULL,NULL,'2024-01-05',1,0),
(115,3,105,1,NULL,'Infanzia dopoguerra','Versione attiva.',NULL,NULL,'2024-04-10',2,1),

-- Gruppo 116
(116,1,100,1,NULL,'Chiesa abbandonata','Luogo.',41.7005,15.7205,'2024-02-14',1,1),

-- Gruppo 117
(117,2,101,1,NULL,'Statuto comunale','Documento.',NULL,NULL,'2023-11-30',1,1),
(117,2,102,NULL,NULL,'Statuto comunale','Aggiornamento.',NULL,NULL,'2024-08-02',2,0),

-- Gruppo 118
(118,3,102,1,NULL,'Memorie contadine','Testimonianza.',NULL,NULL,'2024-03-20',1,1),

-- Gruppo 119
(119,1,103,1,NULL,'Ponte antico','Luogo.',41.7085,15.7310,'2024-01-25',1,1),

-- Gruppo 120
(120,2,104,1,NULL,'Registro scolastico','Prima versione.',NULL,NULL,'2023-10-01',1,0),
(120,2,104,1,NULL,'Registro scolastico','Versione attiva.',NULL,NULL,'2024-02-01',2,1),
(120,2,105,NULL,NULL,'Registro scolastico','Revisione.',NULL,NULL,'2024-06-30',3,0);