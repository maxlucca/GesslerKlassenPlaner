<?php
require_once __DIR__ . '/layout/html_top.php';
?>

    <div class="container">
        <h1>Note eintragen</h1>
        <form method="post" action="../backend/main.php">
            <input type="hidden" name="type" value="addGrade">

            <div>
                <label for="klasse">Klasse:</label>
                <select id="klasse" name="klasse_id" required onchange="loadStudentsAndTests()">
                    <option value="">-- Klasse wählen --</option>
                </select>
            </div>

            <div>
                <label for="schueler">Schüler:</label>
                <select id="schueler" name="schueler_id" required onchange="onStudentChange()">
                    <option value="">-- Schüler wählen --</option>
                </select>
            </div>

            <div>
                <label for="test">Klassenarbeit:</label>
                <select id="test" name="klassenarbeit_id" required onchange="onTestChange()">
                    <option value="">-- Klassenarbeit wählen --</option>
                </select>
            </div>

            <div class="input-small">
                <label for="note">Note:</label>
                <input type="number" id="note" name="note" min="1" max="6" step="0.1" placeholder="z.B. 2.5" required>
            </div>

            <button type="submit">Speichern</button>
            <br>
            <button type="button" onclick="window.location.href='./Startseite.php'" style="margin-top: 2%;">Zurück</button>
        </form>
    </div>

    <script>
        // Load classes from backend
        async function loadClasses() {
            try {
                const response = await fetch('../backend/main.php?type=getClasses');
                const classes = await response.json();
                const select = document.getElementById('klasse');
                
                classes.forEach(klasse => {
                    const option = document.createElement('option');
                    option.value = klasse.klasse_id;
                    option.textContent = klasse.bezeichnung + (klasse.schuljahr ? ` (${klasse.schuljahr})` : '');
                    select.appendChild(option);
                });
            } catch (error) {
                console.error('Fehler beim Laden der Klassen:', error);
            }
        }

        // Load students for selected class
        async function loadStudentsForClass(classId, selectedStudentId = '') {
            try {
                const response = await fetch(`../backend/main.php?type=getStudentsByClass&klasse_id=${classId}`);
                const students = await response.json();
                const select = document.getElementById('schueler');
                select.innerHTML = '<option value="">-- Schüler wählen --</option>';
                
                students.forEach(student => {
                    const option = document.createElement('option');
                    option.value = student.schueler_id;
                    option.textContent = student.vorname + ' ' + student.nachname;
                    select.appendChild(option);
                });

                if (selectedStudentId && select.querySelector(`option[value="${selectedStudentId}"]`)) {
                    select.value = selectedStudentId;
                }
            } catch (error) {
                console.error('Fehler beim Laden der Schüler:', error);
            }
        }

        // Load tests for selected class
        async function loadTestsForClass(classId, selectedTestId = '') {
            try {
                const response = await fetch(`../backend/main.php?type=getTestsByClass&klasse_id=${classId}`);
                const tests = await response.json();
                const select = document.getElementById('test');
                select.innerHTML = '<option value="">-- Klassenarbeit wählen --</option>';
                
                tests.forEach(test => {
                    const option = document.createElement('option');
                    option.value = test.klassenarbeit_id;
                    option.textContent = test.titel + ' (' + test.datum + ')';
                    select.appendChild(option);
                });

                if (selectedTestId && select.querySelector(`option[value="${selectedTestId}"]`)) {
                    select.value = selectedTestId;
                }
            } catch (error) {
                console.error('Fehler beim Laden der Klassenarbeiten:', error);
            }
        }

        // Load tests for a specific student (excluding tests they already have grades in)
        async function loadTestsForStudent(classId, studentId, selectedTestId = '') {
            try {
                const response = await fetch(`../backend/main.php?type=getTestsByClassAndStudent&klasse_id=${classId}&schueler_id=${studentId}`);
                const tests = await response.json();
                const select = document.getElementById('test');
                select.innerHTML = '<option value="">-- Klassenarbeit wählen --</option>';
                
                tests.forEach(test => {
                    const option = document.createElement('option');
                    option.value = test.klassenarbeit_id;
                    option.textContent = test.titel + ' (' + test.datum + ')';
                    select.appendChild(option);
                });

                if (selectedTestId && select.querySelector(`option[value="${selectedTestId}"]`)) {
                    select.value = selectedTestId;
                }
            } catch (error) {
                console.error('Fehler beim Laden der Klassenarbeiten:', error);
            }
        }

        // Load students for a specific test (excluding students who already have grades in this test)
        async function loadStudentsForTest(classId, testId, selectedStudentId = '') {
            try {
                const response = await fetch(`../backend/main.php?type=getStudentsByClassAndTest&klasse_id=${classId}&klassenarbeit_id=${testId}`);
                const students = await response.json();
                const select = document.getElementById('schueler');
                select.innerHTML = '<option value="">-- Schüler wählen --</option>';
                
                students.forEach(student => {
                    const option = document.createElement('option');
                    option.value = student.schueler_id;
                    option.textContent = student.vorname + ' ' + student.nachname;
                    select.appendChild(option);
                });

                if (selectedStudentId && select.querySelector(`option[value="${selectedStudentId}"]`)) {
                    select.value = selectedStudentId;
                }
            } catch (error) {
                console.error('Fehler beim Laden der Schüler:', error);
            }
        }

        // Load both students and tests when class is selected
        function loadStudentsAndTests() {
            const classId = document.getElementById('klasse').value;
            if (classId) {
                loadStudentsForClass(classId);
                loadTestsForClass(classId);
                document.getElementById('schueler').value = '';
                document.getElementById('test').value = '';
            }
        }

        // Handle student change - reload tests for this student
        function onStudentChange() {
            const classId = document.getElementById('klasse').value;
            const studentId = document.getElementById('schueler').value;
            const currentTestId = document.getElementById('test').value;
            if (classId && studentId) {
                loadTestsForStudent(classId, studentId, currentTestId);
            } else if (classId) {
                loadTestsForClass(classId, currentTestId);
            }
        }

        // Handle test change - preserve the student list and keep the current student selected
        function onTestChange() {
            const classId = document.getElementById('klasse').value;
            const currentStudentId = document.getElementById('schueler').value;
            if (classId) {
                loadStudentsForClass(classId, currentStudentId);
            }
        }
        
        // Load classes when page loads
        document.addEventListener('DOMContentLoaded', () => {
            loadClasses();
        });
    </script>

<?php
require_once __DIR__ . '/layout/html_bottom.php';
?>
