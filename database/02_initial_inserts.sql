USE schulverwaltung;

-- ========================
-- Fächer
-- ========================
INSERT INTO fach (name) VALUES
('Mathematik'),
('Deutsch'),
('Englisch');

-- ========================
-- Klassen
-- ========================
INSERT INTO klasse (bezeichnung, schuljahr) VALUES
('E2FI', '2025/2026'),
('E3FI', '2025/2026');

-- ========================
-- Schüler
-- ========================
INSERT INTO schueler (vorname, nachname, geburtsdatum, klasse_id) VALUES
('Anna', 'Müller', '2010-05-12', 1),
('Lukas', 'Schmidt', '2010-08-20', 1),
('Sophia', 'Meier', '2009-11-02', 2),
('Max', 'Fischer', '2010-03-15', 2),
('Emma', 'Weber', '2010-07-09', 1);

-- ========================
-- Klassenarbeiten
-- ========================
INSERT INTO klassenarbeit (titel, datum, gewichtung, fach_id, klasse_id) VALUES
('KA1 Bruchrechnung', '2026-02-01', 1.0, 1, 1),   -- Mathe, E2FI
('KA2 Algebra', '2026-02-15', 1.5, 1, 1),         -- Mathe, E2FI
('KA1 Grammatik', '2026-02-03', 1.0, 2, 1),       -- Deutsch, E2FI
('KA1 Leseverstehen', '2026-02-04', 1.0, 3, 1),   -- Englisch, E2FI
('KA1 Bruchrechnung', '2026-02-05', 1.0, 1, 2),   -- Mathe, E3FI
('KA1 Aufsatz', '2026-02-06', 1.0, 2, 2);         -- Deutsch, E3FI

-- ========================
-- Noten
-- ========================
-- E2FI Schüler
INSERT INTO note (schueler_id, klassenarbeit_id, note) VALUES
(1, 1, 2.0),
(1, 2, 1.7),
(1, 3, 2.3),
(1, 4, 1.3),
(2, 1, 3.0),
(2, 2, 2.7),
(2, 3, 3.3),
(2, 4, 2.0),
(5, 1, 1.3),
(5, 2, 1.0),
(5, 3, 1.7),
(5, 4, 1.3);

-- E3FI Schüler
INSERT INTO note (schueler_id, klassenarbeit_id, note) VALUES
(3, 5, 2.0),
(3, 6, 2.3),
(4, 5, 3.0),
(4, 6, 3.7);