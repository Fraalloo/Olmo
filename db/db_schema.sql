-- Parte DDL

CREATE DATABASE Olmo13;
USE Olmo13;

CREATE TABLE utenti(
    id_utente INT NOT NULL AUTO_INCREMENT, 
    nome_utente VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    pfp VARCHAR(255),
    data_registrazione DATE NOT NULL DEFAULT CURRENT_DATE,
    is_admin BOOLEAN NOT NULL DEFAULT 0,

    CONSTRAINT PK_utenti PRIMARY KEY(id_utente),

    CONSTRAINT CK_utenti_is_admin CHECK(is_admin IN (0,1)),
    CONSTRAINT CK_nome_lenght CHECK(LENGTH(nome_utente) > 3)
);

CREATE TABLE tipi_articoli(
    id_tipo_articolo INT NOT NULL AUTO_INCREMENT, 
    descrizione VARCHAR(20) NOT NULL UNIQUE,

    CONSTRAINT PK_tipi_articoli PRIMARY KEY(id_tipo_articolo)
);

CREATE TABLE gruppi_articoli(
    id_gruppo_articolo INT NOT NULL AUTO_INCREMENT,
    data_creazione DATE NOT NULL DEFAULT CURRENT_DATE,

    CONSTRAINT PK_gruppi_articoli PRIMARY KEY(id_gruppo_articolo)
);

CREATE TABLE articoli(
    id_articolo INT NOT NULL AUTO_INCREMENT,
    id_gruppo_articolo INT NOT NULL,
    id_tipo_articolo INT NOT NULL,
    id_pubblicatore INT NOT NULL,
    id_admin INT NULL,
    banner VARCHAR(255) NULL,
    titolo VARCHAR(100) NOT NULL,
    descrizione TEXT NOT NULL,
    latitudine DECIMAL(9,6) NULL,
    longitudine DECIMAL(9,6) NULL,
    data_pubblicazione DATE NOT NULL DEFAULT CURRENT_DATE,
    versione INT NOT NULL DEFAULT 1,
    is_active BOOLEAN NOT NULL DEFAULT 0,

    CONSTRAINT PK_articoli PRIMARY KEY(id_articolo),

    CONSTRAINT FK1_articoli_gruppo FOREIGN KEY(id_gruppo_articolo)
        REFERENCES gruppi_articoli(id_gruppo_articolo)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT FK2_articoli_tipi FOREIGN KEY(id_tipo_articolo)
        REFERENCES tipi_articoli(id_tipo_articolo)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT FK3_articoli_utenti FOREIGN KEY(id_pubblicatore)
        REFERENCES utenti(id_utente)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT FK4_articoli_admin FOREIGN KEY(id_admin)
        REFERENCES utenti(id_utente)
        ON UPDATE CASCADE
        ON DELETE SET NULL,

    CONSTRAINT CK_articoli_is_active CHECK(is_active IN (0,1)),
    CONSTRAINT CK_articoli_latitudine CHECK(latitudine IS NULL OR latitudine BETWEEN -90 AND 90),
    CONSTRAINT CK_articoli_longitudine CHECK(longitudine IS NULL OR longitudine BETWEEN -180 AND 180),
    CONSTRAINT CK_articoli_versione CHECK(versione >= 1),

    CONSTRAINT UQ_articoli_gruppo_versione UNIQUE(id_gruppo_articolo, versione)
);

CREATE INDEX idx_articoli_tipo ON articoli(id_tipo_articolo);
CREATE INDEX idx_articoli_pubblicatore ON articoli(id_pubblicatore);
CREATE INDEX idx_articoli_admin ON articoli(id_admin);
CREATE INDEX idx_articoli_data_pubblicazione ON articoli(data_pubblicazione);
CREATE INDEX idx_articoli_attivi ON articoli(is_active);

CREATE TABLE file_articoli(
    id_file INT NOT NULL AUTO_INCREMENT,
    id_articolo INT NOT NULL,
    nome_originale VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    data_upload DATE NOT NULL DEFAULT CURRENT_DATE,

    CONSTRAINT PK_file_articoli PRIMARY KEY(id_file),

    CONSTRAINT FK_file_articoli FOREIGN KEY(id_articolo)
        REFERENCES articoli(id_articolo)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

CREATE INDEX idx_file_articoli_articolo ON file_articoli(id_articolo);

CREATE TABLE link(
    id_link INT NOT NULL AUTO_INCREMENT, 
    url_link VARCHAR(255) NOT NULL UNIQUE,

    CONSTRAINT PK_link PRIMARY KEY(id_link)
);

CREATE TABLE link_articoli(
    id_link_articolo INT NOT NULL AUTO_INCREMENT, 
    id_articolo INT NOT NULL,
    id_link INT NOT NULL,

    CONSTRAINT PK_link_articoli PRIMARY KEY(id_link_articolo),

    CONSTRAINT FK1_link_articoli FOREIGN KEY(id_articolo)
        REFERENCES articoli(id_articolo)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT FK2_link_link FOREIGN KEY(id_link)
        REFERENCES link(id_link)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT UQ_link_articolo UNIQUE(id_articolo, id_link)
);

CREATE INDEX idx_link_articoli_articolo ON link_articoli(id_articolo);
CREATE INDEX idx_link_articoli_link ON link_articoli(id_link);


-- Parte DML

-- Tipi articoli previsti
INSERT INTO tipi_articoli(descrizione)
VALUES
    ('luogo'),
    ('documento'),
    ('testimonianza');

-- Amministratore
INSERT INTO utenti(nome_utente, password_hash, is_admin)
VALUES ("DBAdmin", "$2y$12$MFMnmfE16pJ8b5w30SLBoepi3T4BRhTjhvK.gTEyutNqKr9C/XuVS", 1)