<?php
require_once __DIR__ . '/../../new/layout/html_top.php';
require_once __DIR__ . '/../backend/db.php';
?>

<div class="container">
    <h1>Noten Übersicht</h1>

    <!-- Filter-Formular -->
    <div class="card-panel">
        <h3>Noten nach Schüler</h3>
        <label for="classSelectStudent">Klasse wählen:</label>
        <select id="classSelectStudent" onchange="loadStudentsForGrades()">
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

        <label for="studentSelect" style="margin-top: 1rem;">Schüler wählen:</label>
        <select id="studentSelect" onchange="getGradesPerStudent()" disabled>
            <option value="">-- Schüler auswählen --</option>
        </select>
    </div>

    <!-- Results Container for Student -->
    <div id="gradesResult" style="display:none; margin-bottom: 20px;"></div>

    <div class="card-panel">
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

    <div class="card-panel">
        <h3>Noten nach Fach</h3>
        <label for="classSelectSubject">Klasse wählen:</label>
        <select id="classSelectSubject" onchange="loadSubjectsForGrades()">
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

        <label for="subjectSelect" style="margin-top: 1rem;">Fach wählen:</label>
        <select id="subjectSelect" onchange="getGradesPerSubject()" disabled>
            <option value="">-- Fach auswählen --</option>
        </select>
    </div>

    <!-- Results Container for Subject -->
    <div id="subjectResult" style="display:none; margin-bottom: 20px;"></div>

    <br>
    <button type="button" onclick="window.location.href='./Startseite.php'">Zurück</button>
</div>

<script>
function loadStudentsForGrades() {
    const classSelect = document.getElementById('classSelectStudent');
    const studentSelect = document.getElementById('studentSelect');
    const resultDiv = document.getElementById('gradesResult');
    
    if (classSelect.value === '') {
        studentSelect.innerHTML = '<option value="">-- Schüler auswählen --</option>';
        studentSelect.disabled = true;
        resultDiv.style.display = 'none';
        return;
    }
    
    const classId = classSelect.value;
    
    // Fetch students for the selected class
    fetch(`../backend/main.php?type=getStudentsByClass&klasse_id=${encodeURIComponent(classId)}`)
        .then(response => response.json())
        .then(students => {
            studentSelect.innerHTML = '<option value="">-- Schüler auswählen --</option>';
            students.forEach(student => {
                const option = document.createElement('option');
                option.value = student.vorname + '|' + student.nachname;
                option.textContent = student.vorname + ' ' + student.nachname;
                studentSelect.appendChild(option);
            });
            studentSelect.disabled = false;
            resultDiv.style.display = 'none';
        })
        .catch(error => {
            console.error('Fehler:', error);
            studentSelect.innerHTML = '<option>Fehler beim Laden</option>';
            studentSelect.disabled = true;
        });
}

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
    const subjectSelect = document.getElementById('subjectSelect');
    const classSelect = document.getElementById('classSelectSubject');
    const resultDiv = document.getElementById('subjectResult');
    
    if (subjectSelect.value === '') {
        resultDiv.style.display = 'none';
        return;
    }
    
    const subject = subjectSelect.value;
    const classId = classSelect.value;
    
    // Call the backend API using existing gradesPerSubject case with class filter
    const url = classId 
        ? `../backend/main.php?type=gradesPerSubject&fach=${encodeURIComponent(subject)}&klasse_id=${encodeURIComponent(classId)}`
        : `../backend/main.php?type=gradesPerSubject&fach=${encodeURIComponent(subject)}`;
    
    fetch(url)
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

function loadSubjectsForGrades() {
    const classSelect = document.getElementById('classSelectSubject');
    const subjectSelect = document.getElementById('subjectSelect');
    const resultDiv = document.getElementById('subjectResult');
    
    if (classSelect.value === '') {
        subjectSelect.innerHTML = '<option value="">-- Fach auswählen --</option>';
        subjectSelect.disabled = true;
        resultDiv.style.display = 'none';
        return;
    }
    
    const classId = classSelect.value;
    
    // Fetch subjects for the selected class
    fetch(`../backend/main.php?type=getSubjectsByClass&klasse_id=${encodeURIComponent(classId)}`)
        .then(response => response.json())
        .then(subjects => {
            subjectSelect.innerHTML = '<option value="">-- Fach auswählen --</option>';
            subjects.forEach(subject => {
                const option = document.createElement('option');
                option.value = subject.name;
                option.textContent = subject.name;
                subjectSelect.appendChild(option);
            });
            subjectSelect.disabled = false;
            resultDiv.style.display = 'none';
        })
        .catch(error => {
            console.error('Fehler:', error);
            subjectSelect.innerHTML = '<option>Fehler beim Laden</option>';
            subjectSelect.disabled = true;
        });
}
</script>

<?php
require_once __DIR__ . '/../../new/layout/html_bottom.php';
?>