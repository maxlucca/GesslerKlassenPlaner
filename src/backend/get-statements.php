<?php
  // ganze Klasse
  // einzelnes Fach vs alle Fächer

  // einzelner Schüle
  // kompletter Durchschnitt
  // Einzelne Fächer inkl einzelner Noten
  function getStatements() {
    global $pdo;
    
    $type = (isset($_GET['type'])) ? htmlspecialchars($_GET['type']) : "";
    $class_id = (isset($_GET['klasse_id'])) ? htmlspecialchars($_GET['klasse_id']) : "";
    $subject = (isset($_GET['fach'])) ? htmlspecialchars($_GET['fach']) : "";
    $name = (isset($_GET['vorname'])) ? htmlspecialchars($_GET['vorname']) : "";
    $lastname = (isset($_GET['nachname'])) ? htmlspecialchars($_GET['nachname']) : "";
    $bday = (isset($_GET['geb'])) ? htmlspecialchars($_GET['geb']) : "";

    switch($type) {
      case 'getClasses': {
        // Retrieve all classes and return as JSON
        $stmt = $pdo->prepare("SELECT klasse_id, bezeichnung, schuljahr FROM klasse ORDER BY bezeichnung");
        $stmt->execute();
        $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        echo json_encode($classes);
        exit;
      }
      case 'getStudentById': {
        $student_id = (isset($_GET['schueler_id'])) ? intval($_GET['schueler_id']) : 0;
        $stmt = $pdo->prepare("SELECT schueler_id, vorname, nachname, geburtsdatum, klasse_id FROM schueler WHERE schueler_id = ?");
        $stmt->execute([$student_id]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        echo json_encode($student ?: new stdClass());
        exit;
      }
      case 'getSubjects': {
        // Retrieve all subjects and return as JSON
        $stmt = $pdo->prepare("SELECT fach_id, name FROM fach ORDER BY name");
        $stmt->execute();
        $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        echo json_encode($subjects);
        exit;
      }
      case 'getStudentsByClass': {
        // Retrieve all students for a specific class and return as JSON
        $stmt = $pdo->prepare("SELECT schueler_id, vorname, nachname FROM schueler WHERE klasse_id = ? ORDER BY nachname, vorname");
        $stmt->execute([$class_id]);
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        echo json_encode($students);
        exit;
      }
      case 'getTestsByClass': {
        // Retrieve all tests for a specific class and return as JSON
        $stmt = $pdo->prepare("SELECT klassenarbeit_id, titel, datum FROM klassenarbeit WHERE klasse_id = ? ORDER BY datum DESC");
        $stmt->execute([$class_id]);
        $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        echo json_encode($tests);
        exit;
      }
      case 'getTestsByClassAndStudent': {
        // Retrieve tests for a specific class that a student doesn't have grades in
        $student_id = (isset($_GET['schueler_id'])) ? htmlspecialchars($_GET['schueler_id']) : "";
        $stmt = $pdo->prepare(
          "SELECT klassenarbeit_id, titel, datum 
           FROM klassenarbeit 
           WHERE klasse_id = ? 
           AND klassenarbeit_id NOT IN (
             SELECT klassenarbeit_id FROM note WHERE schueler_id = ?
           )
           ORDER BY datum DESC"
        );
        $stmt->execute([$class_id, $student_id]);
        $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        echo json_encode($tests);
        exit;
      }
      case 'getStudentsByClassAndTest': {
        // Retrieve students for a specific class that don't have grades in a specific test
        $test_id = (isset($_GET['klassenarbeit_id'])) ? htmlspecialchars($_GET['klassenarbeit_id']) : "";
        $stmt = $pdo->prepare(
          "SELECT schueler_id, vorname, nachname 
           FROM schueler 
           WHERE klasse_id = ? 
           AND schueler_id NOT IN (
             SELECT schueler_id FROM note WHERE klassenarbeit_id = ?
           )
           ORDER BY nachname, vorname"
        );
        $stmt->execute([$class_id, $test_id]);
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        echo json_encode($students);
        exit;
      }
      case 'gradesPerStudent': {

        $stmt = $pdo->prepare(
          "SELECT f.name AS fach, n.note
          FROM note n
          JOIN schueler s ON n.schueler_id = s.schueler_id
          JOIN klassenarbeit k ON n.klassenarbeit_id = k.klassenarbeit_id
          JOIN fach f ON k.fach_id = f.fach_id
          WHERE s.vorname = ? AND s.nachname = ?
          ORDER BY k.datum;");

        $stmt->execute([$name, $lastname]);

        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "<p>$name $lastname</p>"; 
        echo "<table>";
          echo "<tr>";
            echo "<td> Fach </td>";
            echo "<td> Note </td>";
          echo "</tr>";

        foreach ($res as $r) {
          echo "<tr>";
            echo "<td>" . htmlspecialchars($r['fach']) . "</td>";
            echo "<td>" . htmlspecialchars($r['note']) . "</td>";
          echo "</tr>";
          
        }
              
        echo "</table>";
      break;
      }

      case 'gradesPerSubject': {
      
        $stmt = $pdo->prepare(
          "SELECT  k.bezeichnung AS klasse, s.vorname AS schueler, s.nachname AS nachname, n.note AS note
          FROM note n
          JOIN schueler s ON n.schueler_id = s.schueler_id
          JOIN klassenarbeit ka ON n.klassenarbeit_id = ka.klassenarbeit_id
          JOIN fach f ON ka.fach_id = f.fach_id
          JOIN klasse k ON ka.klasse_id = k.klasse_id
          WHERE f.name = ?
          ORDER BY k.bezeichnung, ka.datum;
");

        $stmt->execute([$subject]);

        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "<p>Noten $subject</p>"; 
        echo "<table>";
          echo "<tr>";
            echo "<td> Klasse </td>";
            echo "<td> Schueler </td>";
            echo "<td> Nachname </td>";
            echo "<td> Noten </td>";
          echo "</tr>";

        foreach ($res as $r) {
          echo "<tr>";
            echo "<td>" . htmlspecialchars($r['klasse']) . "</td>";
            echo "<td>" . htmlspecialchars($r['schueler']) . "</td>";
            echo "<td>" . htmlspecialchars($r['nachname']) . "</td>";
            echo "<td>" . htmlspecialchars($r['note']) . "</td>";
          echo "</tr>";
          
        }
              
        echo "</table>";
      break;
      
      }
      case 'gradesPerClass': {
        $getClass = $pdo->prepare("SELECT bezeichnung FROM klasse WHERE klasse_id = $class_id");
        $className = $getClass->execute();

        $stmt = $pdo->prepare(
          "SELECT f.name AS fach, ROUND(AVG(n.note), 2) AS durchschnitt
          FROM note n
          JOIN schueler s ON n.schueler_id = s.schueler_id
          JOIN klassenarbeit ka ON n.klassenarbeit_id = ka.klassenarbeit_id
          JOIN fach f ON ka.fach_id = f.fach_id
          JOIN klasse k ON ka.klasse_id = k.klasse_id
          WHERE k.klasse_id = ?
          GROUP BY k.bezeichnung, f.name
          ORDER BY f.name;");

        $stmt->execute([$class_id]);

        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "<p>Notenübersicht $className</p>"; 
        echo "<table>";
          echo "<tr>";
            echo "<td> Fach </td>";
            echo "<td> Durchschnitt </td>";
          echo "</tr>";

        foreach ($res as $r) {
          echo "<tr>";
            echo "<td>" . htmlspecialchars($r['fach']) . "</td>";
            echo "<td>" . htmlspecialchars($r['durchschnitt']) . "</td>";
          echo "</tr>";   
        }
              
        echo "</table>";
      break;
      }
    }
  }
?>