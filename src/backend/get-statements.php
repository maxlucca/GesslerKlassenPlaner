<?php
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
        $stmt = $pdo->prepare("SELECT fach_id, name FROM fach ORDER BY name");
        $stmt->execute();
        $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        echo json_encode($subjects);
        exit;
      }
      case 'getStudentsByClass': {
        $stmt = $pdo->prepare("SELECT schueler_id, vorname, nachname FROM schueler WHERE klasse_id = ? ORDER BY nachname, vorname");
        $stmt->execute([$class_id]);
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        echo json_encode($students);
        exit;
      }
      case 'getSubjectsByClass': {
        $stmt = $pdo->prepare(
          "SELECT DISTINCT f.fach_id, f.name 
           FROM fach f 
           JOIN klassenarbeit ka ON f.fach_id = ka.fach_id 
           WHERE ka.klasse_id = ? 
           ORDER BY f.name"
        );
        $stmt->execute([$class_id]);
        $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        echo json_encode($subjects);
        exit;
      }
      case 'getTestsByClass': {
        $stmt = $pdo->prepare("SELECT klassenarbeit_id, titel, datum FROM klassenarbeit WHERE klasse_id = ? ORDER BY datum DESC");
        $stmt->execute([$class_id]);
        $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        echo json_encode($tests);
        exit;
      }
      case 'gradesByClasswork': {
        $test_id = (isset($_GET['klassenarbeit_id'])) ? htmlspecialchars($_GET['klassenarbeit_id']) : "";

        $titleStmt = $pdo->prepare("SELECT titel FROM klassenarbeit WHERE klassenarbeit_id = ?");
        $titleStmt->execute([$test_id]);
        $testData = $titleStmt->fetch(PDO::FETCH_ASSOC);
        $testTitle = $testData ? htmlspecialchars($testData['titel']) : 'Unbekannte Klassenarbeit';

        $stmt = $pdo->prepare(
          "SELECT s.vorname, s.nachname, n.note
           FROM schueler s
           LEFT JOIN note n ON s.schueler_id = n.schueler_id AND n.klassenarbeit_id = ?
           WHERE s.klasse_id = ?
           ORDER BY s.nachname, s.vorname"
        );
        $stmt->execute([$test_id, $class_id]);
        $grades = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "<h3 class=\"table-title\">Noten für Klassenarbeit: $testTitle</h3>";
        echo "<table class=\"data-table\">";
          echo "<thead>";
            echo "<tr>";
              echo "<th>Vorname</th>";
              echo "<th>Nachname</th>";
              echo "<th>Note</th>";
            echo "</tr>";
          echo "</thead>";
          echo "<tbody>";
        foreach ($grades as $row) {
          echo "<tr>";
            echo "<td>" . htmlspecialchars($row['vorname']) . "</td>";
            echo "<td>" . htmlspecialchars($row['nachname']) . "</td>";
            echo "<td>" . ($row['note'] === null ? '-' : htmlspecialchars($row['note'])) . "</td>";
          echo "</tr>";
        }
          echo "</tbody>";
        echo "</table>";
        exit;
      }
      case 'getTestsByClassAndStudent': {
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
          "SELECT f.name AS fach, ROUND(AVG(n.note), 2) AS durchschnitt
          FROM note n
          JOIN schueler s ON n.schueler_id = s.schueler_id
          JOIN klassenarbeit k ON n.klassenarbeit_id = k.klassenarbeit_id
          JOIN fach f ON k.fach_id = f.fach_id
          WHERE s.vorname = ? AND s.nachname = ?
          GROUP BY f.name
          ORDER BY f.name;");

        $stmt->execute([$name, $lastname]);

        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "<h3 class=\"table-title\">$name $lastname</h3>";
        echo "<table class=\"data-table\">";
          echo "<thead>";
            echo "<tr>";
              echo "<th>Fach</th>";
              echo "<th>Durchschnitt</th>";
            echo "</tr>";
          echo "</thead>";
          echo "<tbody>";
        foreach ($res as $r) {
          echo "<tr>";
            echo "<td>" . htmlspecialchars($r['fach']) . "</td>";
            echo "<td>" . htmlspecialchars($r['durchschnitt']) . "</td>";
          echo "</tr>";
        }
          echo "</tbody>";
        echo "</table>";
      break;
      }

      case 'gradesPerSubject': {
      
        $where = "WHERE f.name = ?";
        $params = [$subject];
        
        if ($class_id) {
          $where .= " AND k.klasse_id = ?";
          $params[] = $class_id;
        }
        
        $stmt = $pdo->prepare(
          "SELECT  k.bezeichnung AS klasse, s.vorname AS schueler, s.nachname AS nachname, ka.titel AS klassenarbeit, n.note AS note
          FROM note n
          JOIN schueler s ON n.schueler_id = s.schueler_id
          JOIN klassenarbeit ka ON n.klassenarbeit_id = ka.klassenarbeit_id
          JOIN fach f ON ka.fach_id = f.fach_id
          JOIN klasse k ON ka.klasse_id = k.klasse_id
          $where
          ORDER BY s.nachname, s.vorname, ka.datum;
");

        $stmt->execute($params);

        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "<h3 class=\"table-title\">Noten $subject</h3>";
        echo "<table class=\"data-table\">";
          echo "<thead>";
            echo "<tr>";
              echo "<th>Klasse</th>";
              echo "<th>Vorname</th>";
              echo "<th>Nachname</th>";
              echo "<th>Klassenarbeit</th>";
              echo "<th>Noten</th>";
            echo "</tr>";
          echo "</thead>";
          echo "<tbody>";
        foreach ($res as $r) {
          echo "<tr>";
            echo "<td>" . htmlspecialchars($r['klasse']) . "</td>";
            echo "<td>" . htmlspecialchars($r['schueler']) . "</td>";
            echo "<td>" . htmlspecialchars($r['nachname']) . "</td>";
            echo "<td>" . htmlspecialchars($r['klassenarbeit']) . "</td>";
            echo "<td>" . htmlspecialchars($r['note']) . "</td>";
          echo "</tr>";
        }
          echo "</tbody>";
        echo "</table>";
      break;
      
      }
      case 'gradesPerClass': {
        $getClass = $pdo->prepare("SELECT bezeichnung FROM klasse WHERE klasse_id = ?");
        $getClass->execute([$class_id]);
        $classData = $getClass->fetch(PDO::FETCH_ASSOC);
        $className = $classData ? htmlspecialchars($classData['bezeichnung']) : 'Unbekannt';

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

        echo "<h3 class=\"table-title\">Noten in Klasse $className</h3>";
        echo "<table class=\"data-table\">";
          echo "<thead>";
            echo "<tr>";
              echo "<th>Fach</th>";
              echo "<th>Durchschnitt</th>";
            echo "</tr>";
          echo "</thead>";
          echo "<tbody>";
        foreach ($res as $r) {
          echo "<tr>";
            echo "<td>" . htmlspecialchars($r['fach']) . "</td>";
            echo "<td>" . htmlspecialchars($r['durchschnitt']) . "</td>";
          echo "</tr>";   
        }
          echo "</tbody>";
        echo "</table>";
      break;
      }
    }
  }
?>