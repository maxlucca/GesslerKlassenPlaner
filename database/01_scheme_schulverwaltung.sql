CREATE SCHEMA IF NOT EXISTS schulverwaltung
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE schulverwaltung;

-- ========================
-- Tabelle: klasse
-- ========================
CREATE TABLE klasse (
    klasse_id INT AUTO_INCREMENT PRIMARY KEY,
    bezeichnung VARCHAR(20) NOT NULL,
    schuljahr VARCHAR(9),
    CONSTRAINT uq_klasse UNIQUE (bezeichnung, schuljahr)
);

-- ========================
-- Tabelle: fach (Stammdaten)
-- ========================
CREATE TABLE fach (
    fach_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    CONSTRAINT uq_fach UNIQUE (name)
);

-- ========================
-- Tabelle: schueler
-- ========================
CREATE TABLE schueler (
    schueler_id INT AUTO_INCREMENT PRIMARY KEY,
    vorname VARCHAR(50) NOT NULL,
    nachname VARCHAR(50) NOT NULL,
    geburtsdatum DATE,
    klasse_id INT NOT NULL,
    CONSTRAINT fk_schueler_klasse
        FOREIGN KEY (klasse_id)
        REFERENCES klasse(klasse_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);

-- ========================
-- Tabelle: klassenarbeit
-- ========================
CREATE TABLE klassenarbeit (
    klassenarbeit_id INT AUTO_INCREMENT PRIMARY KEY,
    titel VARCHAR(100) NOT NULL,
    datum DATE NOT NULL,
    gewichtung DECIMAL(4,2) DEFAULT 1.00,
    fach_id INT NOT NULL,
    klasse_id INT NOT NULL,

    CONSTRAINT chk_gewichtung CHECK (gewichtung > 0),

    CONSTRAINT fk_arbeit_fach
        FOREIGN KEY (fach_id)
        REFERENCES fach(fach_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_arbeit_klasse
        FOREIGN KEY (klasse_id)
        REFERENCES klasse(klasse_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

-- ========================
-- Tabelle: note
-- ========================
CREATE TABLE note (
    note_id INT AUTO_INCREMENT PRIMARY KEY,
    schueler_id INT NOT NULL,
    klassenarbeit_id INT NOT NULL,
    note DECIMAL(3,1) NOT NULL,

    CONSTRAINT chk_note CHECK (note BETWEEN 1.0 AND 6.0),

    CONSTRAINT uq_note UNIQUE (schueler_id, klassenarbeit_id),

    CONSTRAINT fk_note_schueler
        FOREIGN KEY (schueler_id)
        REFERENCES schueler(schueler_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_note_arbeit
        FOREIGN KEY (klassenarbeit_id)
        REFERENCES klassenarbeit(klassenarbeit_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);