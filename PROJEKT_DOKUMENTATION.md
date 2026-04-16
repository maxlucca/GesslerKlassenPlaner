# Gessler Klassen Planer - Dokumentation

## 1. Überblick

`Gessler Klassen Planer` ist ein kleines Webprojekt zur Verwaltung von Klassen, Schülern und Noten. Es bietet eine einfache Benutzeroberfläche zur Eingabe, Anzeige und Bearbeitung von Schulverwaltungsdaten.

## 2. Projektstruktur

- `README.md` – Projektübersicht und Grundinformationen.
- `PROJEKT_DOKUMENTATION.md` – Diese Dokumentation.
- `docker-compose.yml` – Docker-Compose-Konfiguration (falls verwendet).
- `Dockerfile` – Docker-Image-Definition.
- `database/` – SQL-Dateien für Schema und Beispiel-Daten.
  - `01_scheme_schulverwaltung.sql`
  - `02_initial_inserts.sql`
- `src/` – Hauptanwendung.
  - `backend/` – Backend-Logik und Datenbankzugriffe.
    - `db.php` – Datenbankverbindung.
    - `get-statements.php` – GET-Endpunkte und dynamische Abfragen.
    - `main.php` – zentrale Auswertungs- und Weiterleitungslogik.
    - `post-statements.php` – POST-Verarbeitung für Formulare.
  - `frontend/` – PHP-Frontend-Seiten und Styles.
    - `klasse_erstellen.php` – Seite zum Anlegen einer Klasse.
    - `klassenarbeit_erstellen.php` – Seite zum Anlegen einer Klassenarbeit.
    - `note_eintragen.php` – Seite zum Eintragen von Noten.
    - `schueler_erstellen.php` – Seite zum Anlegen und Bearbeiten von Schülern.
    - `Startseite.php` – Startseite mit Navigation.
    - `styles.css` – globale Gestaltung der Oberfläche.
    - `uebersicht.php` – Noten-Übersicht mit Filterfunktionen.
    - `uebersicht_schueler.php` – Übersicht aller Schüler.

## 3. Hauptfunktionen

### 3.1 Klassenverwaltung

- Klasse anlegen
- Klassenliste anzeigen (über Filter und Auswertung)

### 3.2 Schülerverwaltung

- Schüler anlegen
- Schüler bearbeiten
- Schüler löschen
- Schülerübersicht nach Klasse filtern

### 3.3 Notenverwaltung

- Noten eintragen für Schüler und Klassenarbeiten
- Notenübersichten nach Schüler, Klasse und Fach
- Dynamische Filter in der Notenübersicht

## 4. Installation

1. Repository klonen:

```bash
git clone https://github.com/maxweber-jent/GesslerKlassenPlaner.git
cd GesslerKlassenPlaner
```

2. Datenbank einrichten:

- Die SQL-Dateien im Ordner `database/` enthalten Schema und Beispiel-Daten.
- Importieren in eine MySQL-/MariaDB-Datenbank.

3. Webserver konfigurieren:

- Das Projekt kann mit einem lokalen PHP-Webserver ausgeführt werden.
- Beispiel:

```bash
php -S localhost:8000 -t src/frontend
```

4. Pfadkonfiguration prüfen:

- `src/backend/db.php` muss so konfiguriert sein, dass eine Verbindung zur Datenbank hergestellt werden kann.

## 5. Datenbank

### 5.1 Schema

- `klasse` – enthält Klassen und Schuljahre.
- `schueler` – enthält Schülerdaten, zugeordnete Klasse und Geburtsdatum.
- `fach` – enthält Fächer.
- `klassenarbeit` – enthält Tests/Klassenarbeiten und deren Gewichtung.
- `note` – enthält Noten pro Schüler und Klassenarbeit.

### 5.2 Beispiel-Daten

- `02_initial_inserts.sql` legt erste Beispiel-Datensätze an.

## 6. Backend-Logik

### 6.1 `src/backend/db.php`

- Baut PDO-Verbindung zur Datenbank auf.
- Wird von allen Backend-Seiten eingebunden.

### 6.2 `src/backend/get-statements.php`

- Verarbeitet GET-Anfragen mit verschiedenen Typen.
- Beispiele:
  - `type=getClasses`
  - `type=getStudentsByClass`
  - `type=getSubjectsByClass`
  - `type=gradesByClasswork`
  - `type=gradesPerStudent`
  - `type=gradesPerClass`
  - `type=gradesPerSubject`

### 6.3 `src/backend/post-statements.php`

- Verarbeitet Formular-POSTs für Aktionen wie:
  - `addStudent`
  - `updateStudent`
  - `removeStudent`
  - `addGrade`
  - `addKlassenarbeit`

## 7. Frontend-Seiten

### 7.1 `Startseite.php`

- Startseite mit Navigation zu den wichtigsten Funktionen.
- Trennt Eintrag-/Erstellungsfunktionen von Übersichtsseiten.

### 7.2 `klasse_erstellen.php`

- Formular zum Anlegen einer neuen Klasse.

### 7.3 `schueler_erstellen.php`

- Formular zum Anlegen oder Bearbeiten eines Schülers.
- Unterstützt Weiterleitung zurück zur Startseite mit Erfolgsmeldung.

### 7.4 `note_eintragen.php`

- Formular zum Eintragen einer Note für einen Schüler und eine Klassenarbeit.
- Dynamische Dropdowns: Klasse → Schüler → Klassenarbeit.
- Verhindert, dass Auswahlfelder beim Nachladen verloren gehen.

### 7.5 `klassenarbeit_erstellen.php`

- Formular zum Anlegen einer neuen Klassenarbeit.
- Gewichtung, Fach und Klasse werden gewählt.

### 7.6 `uebersicht.php`

- Anzeige von Noten-Übersichten:
  - nach Schüler
  - nach Klasse
  - nach Fach
  - nach Klassenarbeit innerhalb einer Klasse
- Filter und Auswahlfelder sind dynamisch.


### 7.7 `uebersicht_schueler.php`

- Schülerliste mit Klassenfilter.
- Bearbeiten- und Löschen-Links pro Schüler.
- Delete-Link in Rot für bessere Erkennbarkeit.

## 8. Styling

- `src/frontend/styles.css` definiert das Design.
- Wichtige Klassen:
  - `.container`
  - `.menu-card`
  - `.grid`
  - `.card-panel`
  - `.data-table`
  - `.menu-section`
  - `.input-small`
  - `.delete-link`

## 9. Erweiterungsmöglichkeiten

- Mehr Felder für Schüler (z. B. E-Mail, Telefon).
- Benutzer-Login und Rechteverwaltung.
- Exportfunktionen als CSV oder PDF.
- Erweiterte Notenstatistiken und Diagramme.

## 10. Support

- Bei Fragen oder Problemen: Issue im Repository erstellen.
- Alternative: `readme.md` und `PROJEKT_DOKUMENTATION.md` als Einstiegspunkt nutzen.
