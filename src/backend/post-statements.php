<?php

function postStatements() {
  // make PDO connection available from db.php
  global $pdo;

  $type = isset($_POST['type']) ? htmlspecialchars($_POST['type']) : null;
  $class_id = isset($_POST['klasse_id']) ? htmlspecialchars($_POST['klasse_id']) : null;
  $subject = isset($_POST['fach']) ? htmlspecialchars($_POST['fach']) : null;
  $name = isset($_POST['vorname']) ? htmlspecialchars($_POST['vorname']) : null;
  $lastname = isset($_POST['nachname']) ? htmlspecialchars($_POST['nachname']) : null;
  $bday = isset($_POST['geb']) ? htmlspecialchars($_POST['geb']) : null;

  switch ($type) {
    case 'addStudent': {
      // insert a new student record
      $fields = [$name, $lastname, $bday, $class_id];
      $check = $pdo->prepare(
        "SELECT * FROM schueler WHERE vorname = ? and nachname = ? and geburtsdatum = ? and klasse_id = ?"
      );
      $check->execute($fields);
      $exists = $check->fetchAll(PDO::FETCH_ASSOC);

      if ($name && $lastname && $bday && $class_id && !$exists) {
        $stmt = $pdo->prepare(
          "INSERT INTO schueler (vorname, nachname, geburtsdatum, klasse_id) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute($fields);
      }
      break;
    }

// von Tim eingefügt um was zu testen (funktioniert) :)

    case 'addClass': {
      $bezeichnung = isset($_POST['bezeichnung']) ? htmlspecialchars($_POST['bezeichnung']) : null;
      $schuljahr = isset($_POST['schuljahr']) ? htmlspecialchars($_POST['schuljahr']) : null;

      if ($bezeichnung && $schuljahr) {
        $check = $pdo->prepare(
          "SELECT * FROM klasse WHERE bezeichnung = ? AND schuljahr = ?"
        );
        $check->execute([$bezeichnung, $schuljahr]);
        $exists = $check->fetchAll(PDO::FETCH_ASSOC);

        if (!$exists) {
          $stmt = $pdo->prepare(
            "INSERT INTO klasse (bezeichnung, schuljahr) VALUES (?, ?)"
          );
          $stmt->execute([$bezeichnung, $schuljahr]);
        }
      }
      break;
    }

// von Tim eingefügt um was zu testen (funktioniert) :)




    case 'removeStudent': {
      // expects schueler_id in POST data
      $sid = isset($_POST['schueler_id']) ? intval($_POST['schueler_id']) : 0;
      if ($sid) {
        $stmt = $pdo->prepare("DELETE FROM schueler WHERE schueler_id = ?");
        $stmt->execute([$sid]);
      } else {
        echo "<p> Schüler existiert nicht </p>";
      }
      break;
    }

    case 'updateStudent': {
      // update existing student values
      $sid = isset($_POST['schueler_id']) ? intval($_POST['schueler_id']) : 0;
      if ($sid && ($name || $lastname || $bday || $class_id)) {
        $stmt = $pdo->prepare(
          "UPDATE schueler
           SET vorname = ?, nachname = ?, geburtsdatum = ?, klasse_id = ?
           WHERE schueler_id = ?"
        );
        $stmt->execute([$name, $lastname, $bday, $class_id, $sid]);
      }
      break;
    }

    case 'addGrade': {
      // add a new grade/note for a student
      $sid = isset($_POST['schueler_id']) ? intval($_POST['schueler_id']) : 0;
      $arbeit = isset($_POST['klassenarbeit_id']) ? intval($_POST['klassenarbeit_id']) : 0;
      $note = isset($_POST['note']) ? floatval($_POST['note']) : null;
      if ($sid && $arbeit && $note !== null) {
        $stmt = $pdo->prepare(
          "INSERT INTO note (schueler_id, klassenarbeit_id, note) VALUES (?, ?, ?)"
        );
        $stmt->execute([$sid, $arbeit, $note]);
      }
      break;
    }

    default: {
      echo "<p>Fehler, bitte prüfe die Eingabe<p>";
      break;
    }
  }

  // einzelner Schüler
  // Schüler hinzufügen / Entfernen
  // Klassenarbeit hinzufügen / Anpassen / Löschen
}