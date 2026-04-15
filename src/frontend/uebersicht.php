<?php
require_once __DIR__ . '/../../new/layout/html_top.php';
require_once __DIR__ . '/../backend/db.php';

// Fetch all data joined together
$query = "
    SELECT 
        s.schueler_id,
        s.vorname,
        s.nachname,
        s.geburtsdatum,
        k.klasse_id,
        k.bezeichnung as klasse_name,
        k.schuljahr,
        f.fach_id,
        f.name as fach_name,
        ka.klassenarbeit_id,
        ka.titel as exam_title,
        ka.datum as exam_date,
        ka.gewichtung,
        n.note_id,
        n.note
    FROM schueler s
    JOIN klasse k ON s.klasse_id = k.klasse_id
    LEFT JOIN note n ON s.schueler_id = n.schueler_id
    LEFT JOIN klassenarbeit ka ON n.klassenarbeit_id = ka.klassenarbeit_id
    LEFT JOIN fach f ON ka.fach_id = f.fach_id
    ORDER BY k.bezeichnung, s.nachname, s.vorname, f.name, ka.datum
";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $allData = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p>Fehler beim Laden der Daten: " . htmlspecialchars($e->getMessage()) . "</p>";
    $allData = [];
}
?>

<div class="container">
    <h1>Datenbank-Übersicht</h1>

    <!-- Filter-Formular -->
    <div style="background-color: #f0f0f0; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
        <h3>Noten nach Schüler</h3>
        <label for="studentSelect">Schüler wählen:</label>
        <select id="studentSelect" onchange="getGradesPerStudent()">
            <option value="">-- Schüler auswählen --</option>
            <?php
            // Get unique students
            $studentQuery = "SELECT DISTINCT s.vorname, s.nachname 
                           FROM schueler s 
                           ORDER BY s.nachname, s.vorname";
            try {
                $stmt = $pdo->prepare($studentQuery);
                $stmt->execute();
                $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($students as $student) {
                    echo '<option value="' . htmlspecialchars($student['vorname']) . '|' . htmlspecialchars($student['nachname']) . '">';
                    echo htmlspecialchars($student['vorname'] . ' ' . $student['nachname']);
                    echo '</option>';
                }
            } catch (PDOException $e) {
                echo '<option>Fehler beim Laden der Schüler</option>';
            }
            ?>
        </select>
    </div>

    <!-- Results Container for Student -->
    <div id="gradesResult" style="display:none; margin-bottom: 20px;"></div>

    <div style="background-color: #f0f0f0; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
        <h3>Noten nach Klasse</h3>
        <label for="classSelect">Klasse wählen:</label>
        <select id="classSelect" onchange="getGradesPerClass()">
            <option value="">-- Klasse auswählen --</option>
            <?php
            // Get unique classes
            $classQuery = "SELECT DISTINCT k.klasse_id, k.bezeichnung 
                          FROM klasse k 
                          ORDER BY k.bezeichnung";
            try {
                $stmt = $pdo->prepare($classQuery);
                $stmt->execute();
                $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($classes as $class) {
                    echo '<option value="' . htmlspecialchars($class['klasse_id']) . '">';
                    echo htmlspecialchars($class['bezeichnung']);
                    echo '</option>';
                }
            } catch (PDOException $e) {
                echo '<option>Fehler beim Laden der Klassen</option>';
            }
            ?>
        </select>
    </div>

    <!-- Results Container for Class -->
    <div id="classResult" style="display:none; margin-bottom: 20px;"></div>

    <div style="background-color: #f0f0f0; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
        <h3>Noten nach Fach</h3>
        <label for="subjectSelect">Fach wählen:</label>
        <select id="subjectSelect" onchange="getGradesPerSubject()">
            <option value="">-- Fach auswählen --</option>
            <?php
            // Get unique subjects
            $subjectQuery = "SELECT DISTINCT f.fach_id, f.name 
                           FROM fach f 
                           ORDER BY f.name";
            try {
                $stmt = $pdo->prepare($subjectQuery);
                $stmt->execute();
                $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($subjects as $subject) {
                    echo '<option value="' . htmlspecialchars($subject['name']) . '">';
                    echo htmlspecialchars($subject['name']);
                    echo '</option>';
                }
            } catch (PDOException $e) {
                echo '<option>Fehler beim Laden der Fächer</option>';
            }
            ?>
        </select>
    </div>

    <!-- Results Container for Subject -->
    <div id="subjectResult" style="display:none; margin-bottom: 20px;"></div>

    <!-- Main Data Table -->
    <?php
    if (!empty($allData)): ?>
        <table border="1" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr style="background-color: #4CAF50; color: white;">
                    <th>Klasse</th>
                    <th>Vorname</th>
                    <th>Nachname</th>
                    <th>Geburtsdatum</th>
                    <th>Schuljahr</th>
                    <th>Fach</th>
                    <th>Klassenarbeit</th>
                    <th>Datum</th>
                    <th>Note</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
                <?php $seenStudents = []; ?>
                <?php foreach ($allData as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['klasse_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['vorname']); ?></td>
                        <td><?php echo htmlspecialchars($row['nachname']); ?></td>
                        <td><?php echo htmlspecialchars($row['geburtsdatum'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($row['schuljahr'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($row['fach_name'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($row['exam_title'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($row['exam_date'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($row['note'] ?? '-'); ?></td>
                        <td>
                            <?php if (!isset($seenStudents[$row['schueler_id']])): ?>
                                <a href="./schueler_erstellen.php?schueler_id=<?php echo htmlspecialchars($row['schueler_id']); ?>">Bearbeiten</a>
                                <?php $seenStudents[$row['schueler_id']] = true; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p style="margin-top: 20px;"><strong>Gesamtanzahl Einträge:</strong> <?php echo count($allData); ?></p>
    <?php else: ?>
        <p>Keine Daten gefunden.</p>
    <?php endif; ?>
    
    <br>
    <button type="button" onclick="window.location.href='./Startseite.php'">Zurück</button>
</div>

<script>
function getGradesPerStudent() {
    const select = document.getElementById('studentSelect');
    const resultDiv = document.getElementById('gradesResult');
    
    if (select.value === '') {
        resultDiv.style.display = 'none';
        return;
    }
    
    const [vorname, nachname] = select.value.split('|');
    
    // Call the backend API using existing gradesPerStudent case
    fetch(`../backend/main.php?type=gradesPerStudent&vorname=${encodeURIComponent(vorname)}&nachname=${encodeURIComponent(nachname)}`)
        .then(response => response.text())
        .then(html => {
            resultDiv.innerHTML = html;
            resultDiv.style.display = 'block';
        })
        .catch(error => {
            console.error('Fehler:', error);
            resultDiv.innerHTML = '<p style="color: red;">Fehler beim Abrufen der Noten.</p>';
            resultDiv.style.display = 'block';
        });
}

function getGradesPerClass() {
    const select = document.getElementById('classSelect');
    const resultDiv = document.getElementById('classResult');
    
    if (select.value === '') {
        resultDiv.style.display = 'none';
        return;
    }
    
    const classId = select.value;
    
    // Call the backend API using existing gradesPerClass case
    fetch(`../backend/main.php?type=gradesPerClass&klasse_id=${encodeURIComponent(classId)}`)
        .then(response => response.text())
        .then(html => {
            resultDiv.innerHTML = html;
            resultDiv.style.display = 'block';
        })
        .catch(error => {
            console.error('Fehler:', error);
            resultDiv.innerHTML = '<p style="color: red;">Fehler beim Abrufen der Noten.</p>';
            resultDiv.style.display = 'block';
        });
}

function getGradesPerSubject() {
    const select = document.getElementById('subjectSelect');
    const resultDiv = document.getElementById('subjectResult');
    
    if (select.value === '') {
        resultDiv.style.display = 'none';
        return;
    }
    
    const subject = select.value;
    
    // Call the backend API using existing gradesPerSubject case
    fetch(`../backend/main.php?type=gradesPerSubject&fach=${encodeURIComponent(subject)}`)
        .then(response => response.text())
        .then(html => {
            resultDiv.innerHTML = html;
            resultDiv.style.display = 'block';
        })
        .catch(error => {
            console.error('Fehler:', error);
            resultDiv.innerHTML = '<p style="color: red;">Fehler beim Abrufen der Noten.</p>';
            resultDiv.style.display = 'block';
        });
}
</script>

<?php
require_once __DIR__ . '/../../new/layout/html_bottom.php';
?>