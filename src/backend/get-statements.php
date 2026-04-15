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
      case 'gradesPerStudent': {
  
        echo "<p>$vorname $nachname</p>"; 
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
      
        echo "<p>Klassenübersicht</p>"; 
        echo "<table>";
          echo "<tr>";
            echo "<td> Klasse </td>";
            echo "<td> Fach </td>";
            echo "<td> Note </td>";
          echo "</tr>";

        foreach ($res as $r) {
          echo "<tr>";
            echo "<td>" . htmlspecialchars($r['klasse_id']) . "</td>";
            echo "<td>" . htmlspecialchars($r['fach']) . "</td>";
            echo "<td>" . htmlspecialchars($r['note']) . "</td>";
          echo "</tr>";
          
        }
              
        echo "</table>";
      break;

      
      
      }
      case 'gradesPerClass': {

        echo "<p>Notenübersicht</p>"; 
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
    }
  }
?>