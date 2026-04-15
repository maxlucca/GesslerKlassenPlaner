<?php
require_once __DIR__ . '/../../new/layout/html_top.php';
require_once __DIR__ . '/../backend/db.php';
?>

<div class="container">
    <h1>Datenbank-Übersicht</h1>

    <!-- Filter-Formular -->
    <form method="GET" action="">
        <div>
            <label for="filter_type">Ansicht wählen:</label>
            <select id="filter_type" name="type" onchange="toggleFields()">
                <option value="">-- Bitte wählen --</option>
                <option value="getClasses">Alle Klassen</option>
                <option value="gradesPerClass">Notenauszug pro Klasse</option>
                <option value="gradesPerStudent">Notenauszug pro Schüler</option>
                <option value="gradesPerSubject">Notenauszug pro Fach</option>
            </select>
        </div>

        <!-- Zusätzliche Felder für spezifische Filter -->
        <div id="class_field" style="display: none;">
            <label for="klasse_id">Klasse:</label>
            <select id="klasse_id" name="klasse_id">
                <option value="">-- Klasse wählen --</option>
                <?php
                // Klassen aus DB laden
                $stmt = $pdo->prepare("SELECT klasse_id, bezeichnung FROM klasse ORDER BY bezeichnung");
                $stmt->execute();
                $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($classes as $class) {
                    echo "<option value='" . $class['klasse_id'] . "'>" . htmlspecialchars($class['bezeichnung']) . "</option>";
                }
                ?>
            </select>
        </div>

        <div id="student_field" style="display: none;">
            <label for="vorname">Vorname:</label>
            <input type="text" id="vorname" name="vorname" placeholder="Vorname">
            <label for="nachname">Nachname:</label>
            <input type="text" id="nachname" name="nachname" placeholder="Nachname">
            <label for="fach_student">Fach filtern (optional):</label>
            <select id="fach_student" name="fach">
                <option value="">-- Alle Fächer --</option>
                <?php
                // Fächer aus DB laden
                $stmt = $pdo->prepare("SELECT name FROM fach ORDER BY name");
                $stmt->execute();
                $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($subjects as $subj) {
                    echo "<option value='" . htmlspecialchars($subj['name']) . "'>" . htmlspecialchars($subj['name']) . "</option>";
                }
                ?>
            </select>
        </div>

        <div id="subject_field" style="display: none;">
            <label for="fach">Fach:</label>
            <select id="fach" name="fach" required>
                <option value="">-- Fach wählen --</option>
                <?php
                foreach ($subjects as $subj) {
                    echo "<option value='" . htmlspecialchars($subj['name']) . "'>" . htmlspecialchars($subj['name']) . "</option>";
                }
                ?>
            </select>
            <label for="klasse_optional">Klasse (optional):</label>
            <select id="klasse_optional" name="klasse_id">
                <option value="">-- Alle Klassen --</option>
                <?php
                foreach ($classes as $class) {
                    echo "<option value='" . $class['klasse_id'] . "'>" . htmlspecialchars($class['bezeichnung']) . "</option>";
                }
                ?>
            </select>
            <label>
                <input type="checkbox" id="show_average" name="show_average" value="1"> Nur Durchschnitt anzeigen
            </label>
        </div>

        <div id="class_view_field" style="display: none;">
            <label for="view_type">Ansicht:</label>
            <select id="view_type" name="view_type">
                <option value="fach">Pro Fach</option>
                <option value="klassenarbeit">Pro Klassenarbeit</option>
            </select>
        </div>

        <button type="submit">Anzeigen</button>
    </form>

    <!-- Daten-Anzeige (Temporär da cases noch nicht existieren) -->
    <?php
    if (isset($_GET['type'])) {
        $type = htmlspecialchars($_GET['type']);
        $class_id = isset($_GET['klasse_id']) ? htmlspecialchars($_GET['klasse_id']) : "";
        $subject = isset($_GET['fach']) ? htmlspecialchars($_GET['fach']) : "";
        $name = isset($_GET['vorname']) ? htmlspecialchars($_GET['vorname']) : "";
        $lastname = isset($_GET['nachname']) ? htmlspecialchars($_GET['nachname']) : "";
        $show_average = isset($_GET['show_average']);
        $view_type = isset($_GET['view_type']) ? htmlspecialchars($_GET['view_type']) : "fach";

        switch($type) {
            case 'getClasses': {
                $stmt = $pdo->prepare("SELECT klasse_id, bezeichnung, schuljahr FROM klasse ORDER BY bezeichnung");
                $stmt->execute();
                $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo "<h2>Alle Klassen</h2>";
                if ($res) {
                    echo "<table border='1'>";
                    echo "<tr><th>ID</th><th>Bezeichnung</th><th>Schuljahr</th></tr>";
                    foreach ($res as $r) {
                        echo "<tr><td>" . $r['klasse_id'] . "</td><td>" . htmlspecialchars($r['bezeichnung']) . "</td><td>" . htmlspecialchars($r['schuljahr']) . "</td></tr>";
                    }
                    echo "</table>";
                }
                break;
            }

            case 'gradesPerStudent': {
                if (!$name || !$lastname) {
                    echo "<p>Bitte Vor- und Nachname angeben.</p>";
                    break;
                }

                $query = "SELECT f.name as fach, k.titel as klassenarbeit, n.note
                          FROM note n
                          JOIN schueler s ON n.schueler_id = s.schueler_id
                          JOIN klassenarbeit k ON n.klassenarbeit_id = k.klassenarbeit_id
                          JOIN fach f ON k.fach_id = f.fach_id
                          WHERE s.vorname = ? AND s.nachname = ?";

                $params = [$name, $lastname];

                if ($subject) {
                    $query .= " AND f.name = ?";
                    $params[] = $subject;
                }

                $query .= " ORDER BY f.name, k.datum";

                $stmt = $pdo->prepare($query);
                $stmt->execute($params);
                $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo "<h2>Notenauszug für $name $lastname</h2>";
                if ($res) {
                    echo "<table border='1'>";
                    echo "<tr><th>Fach</th><th>Klassenarbeit</th><th>Note</th></tr>";
                    foreach ($res as $r) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($r['fach']) . "</td>";
                        echo "<td>" . htmlspecialchars($r['klassenarbeit']) . "</td>";
                        echo "<td>" . htmlspecialchars($r['note']) . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p>Keine Noten gefunden.</p>";
                }
                break;
            }

            case 'gradesPerClass': {
                if (!$class_id) {
                    echo "<p>Bitte Klasse auswählen.</p>";
                    break;
                }

                if ($view_type == "fach") {
                    $stmt = $pdo->prepare("
                        SELECT f.name as fach, AVG(n.note) as durchschnitt, COUNT(n.note) as anzahl
                        FROM note n
                        JOIN schueler s ON n.schueler_id = s.schueler_id
                        JOIN klassenarbeit k ON n.klassenarbeit_id = k.klassenarbeit_id
                        JOIN fach f ON k.fach_id = f.fach_id
                        WHERE s.klasse_id = ?
                        GROUP BY f.fach_id
                        ORDER BY f.name
                    ");
                    $stmt->execute([$class_id]);
                    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    echo "<h2>Notenübersicht pro Fach für Klasse</h2>";
                    if ($res) {
                        echo "<table border='1'>";
                        echo "<tr><th>Fach</th><th>Durchschnitt</th><th>Anzahl Noten</th></tr>";
                        foreach ($res as $r) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($r['fach']) . "</td>";
                            echo "<td>" . number_format($r['durchschnitt'], 1) . "</td>";
                            echo "<td>" . $r['anzahl'] . "</td>";
                            echo "</tr>";
                        }
                        echo "</table>";
                    }
                } else {
                    $stmt = $pdo->prepare("
                        SELECT k.titel, f.name as fach, k.datum, AVG(n.note) as durchschnitt, COUNT(n.note) as anzahl
                        FROM note n
                        JOIN schueler s ON n.schueler_id = s.schueler_id
                        JOIN klassenarbeit k ON n.klassenarbeit_id = k.klassenarbeit_id
                        JOIN fach f ON k.fach_id = f.fach_id
                        WHERE s.klasse_id = ?
                        GROUP BY k.klassenarbeit_id
                        ORDER BY k.datum
                    ");
                    $stmt->execute([$class_id]);
                    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    echo "<h2>Notenübersicht pro Klassenarbeit für Klasse</h2>";
                    if ($res) {
                        echo "<table border='1'>";
                        echo "<tr><th>Klassenarbeit</th><th>Fach</th><th>Datum</th><th>Durchschnitt</th><th>Anzahl Noten</th></tr>";
                        foreach ($res as $r) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($r['titel']) . "</td>";
                            echo "<td>" . htmlspecialchars($r['fach']) . "</td>";
                            echo "<td>" . htmlspecialchars($r['datum']) . "</td>";
                            echo "<td>" . number_format($r['durchschnitt'], 1) . "</td>";
                            echo "<td>" . $r['anzahl'] . "</td>";
                            echo "</tr>";
                        }
                        echo "</table>";
                    }
                }
                break;
            }

            case 'gradesPerSubject': {
                if (!$subject) {
                    echo "<p>Bitte Fach auswählen.</p>";
                    break;
                }

                $query = "SELECT s.vorname, s.nachname, k.titel as klassenarbeit, n.note, kl.bezeichnung as klasse
                          FROM note n
                          JOIN schueler s ON n.schueler_id = s.schueler_id
                          JOIN klasse kl ON s.klasse_id = kl.klasse_id
                          JOIN klassenarbeit k ON n.klassenarbeit_id = k.klassenarbeit_id
                          JOIN fach f ON k.fach_id = f.fach_id
                          WHERE f.name = ?";

                $params = [$subject];

                if ($class_id) {
                    $query .= " AND s.klasse_id = ?";
                    $params[] = $class_id;
                }

                $query .= " ORDER BY s.nachname, s.vorname, k.datum";

                $stmt = $pdo->prepare($query);
                $stmt->execute($params);
                $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo "<h2>Notenauszug für Fach: $subject</h2>";
                if ($class_id) {
                    echo "<p>Gefiltert nach Klasse</p>";
                }

                if ($res) {
                    if ($show_average) {
                        $total = 0;
                        $count = 0;
                        foreach ($res as $r) {
                            $total += $r['note'];
                            $count++;
                        }
                        $average = $count > 0 ? $total / $count : 0;
                        echo "<p>Durchschnittsnote: " . number_format($average, 1) . " (aus $count Noten)</p>";
                    } else {
                        echo "<table border='1'>";
                        echo "<tr><th>Schüler</th><th>Klasse</th><th>Klassenarbeit</th><th>Note</th></tr>";
                        foreach ($res as $r) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($r['vorname'] . ' ' . $r['nachname']) . "</td>";
                            echo "<td>" . htmlspecialchars($r['klasse']) . "</td>";
                            echo "<td>" . htmlspecialchars($r['klassenarbeit']) . "</td>";
                            echo "<td>" . htmlspecialchars($r['note']) . "</td>";
                            echo "</tr>";
                        }
                        echo "</table>";
                    }
                } else {
                    echo "<p>Keine Noten gefunden.</p>";
                }
                break;
            }

            default: {
                echo "<p>Bitte einen gültigen Filter auswählen.</p>";
            }
        }
    }
    ?>

    <br>
    <button type="button" onclick="window.location.href='./Startseite.php'">Zurück</button>
</div>

<script>
function toggleFields() {
    const type = document.getElementById('filter_type').value;
    const classField = document.getElementById('class_field');
    const studentField = document.getElementById('student_field');
    const subjectField = document.getElementById('subject_field');
    const classViewField = document.getElementById('class_view_field');
    
    if (classField) classField.style.display = (type === 'gradesPerClass') ? 'block' : 'none';
    if (studentField) studentField.style.display = (type === 'gradesPerStudent') ? 'block' : 'none';
    if (subjectField) subjectField.style.display = (type === 'gradesPerSubject') ? 'block' : 'none';
    if (classViewField) classViewField.style.display = (type === 'gradesPerClass') ? 'block' : 'none';
}
</script>

<?php
require_once __DIR__ . '/../../new/layout/html_bottom.php';
?>