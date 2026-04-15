<?php
require_once __DIR__ . '/../../new/layout/html_top.php';
?>



    <div class="container">
        <h1 id="pageTitle">Schüler erstellen</h1>
        <form method="post" action="../backend/main.php">
            <input type="hidden" name="type" id="formType" value="addStudent">
            <input type="hidden" name="schueler_id" id="schueler_id_input" value="">
            
            <div>
                <label for="vorname">Vorname:</label>
                <input type="text" id="vorname" name="vorname" placeholder="z.B. Max" required>
            </div>
            
            <div>
                <label for="nachname">Nachname:</label>
                <input type="text" id="nachname" name="nachname" placeholder="z.B. Mustermann" required>
            </div>
            
            <div>
                <label for="geb">Geburtsdatum:</label>
                <input type="date" id="geb" name="geb" required>
            </div>
            
            <div>
                <label for="klasse">Klasse:</label>
                <select id="klasse" name="klasse_id" required>
                    <option value="">-- Klasse wählen --</option>
                </select>
            </div>
            
            <button type="submit" id="submitButton">Erstellen</button>
             <br>
            <button type="button" onclick="window.location.href='./Startseite.php'" style="margin-top: 2%;">Zurück</button>
        </form>
    </div>

    <script>
        // Load classes from backend
        async function loadClasses(selectedClassId = '') {
            try {
                const response = await fetch('../backend/main.php?type=getClasses');
                const classes = await response.json();
                const select = document.getElementById('klasse');
                select.innerHTML = '<option value="">-- Klasse wählen --</option>';

                classes.forEach(klasse => {
                    const option = document.createElement('option');
                    option.value = klasse.klasse_id;
                    option.textContent = klasse.bezeichnung + (klasse.schuljahr ? ` (${klasse.schuljahr})` : '');
                    select.appendChild(option);
                });

                if (selectedClassId && select.querySelector(`option[value="${selectedClassId}"]`)) {
                    select.value = selectedClassId;
                }
            } catch (error) {
                console.error('Fehler beim Laden der Klassen:', error);
            }
        }

        async function loadStudent(studentId) {
            try {
                const response = await fetch(`../backend/main.php?type=getStudentById&schueler_id=${studentId}`);
                const student = await response.json();

                if (!student || !student.schueler_id) {
                    console.warn('Schüler nicht gefunden:', studentId);
                    return;
                }

                document.getElementById('pageTitle').textContent = 'Schüler bearbeiten';
                document.getElementById('formType').value = 'updateStudent';
                document.getElementById('schueler_id_input').value = student.schueler_id;
                document.getElementById('vorname').value = student.vorname;
                document.getElementById('nachname').value = student.nachname;
                document.getElementById('geb').value = student.geburtsdatum;
                document.getElementById('submitButton').textContent = 'Speichern';

                await loadClasses(student.klasse_id);
            } catch (error) {
                console.error('Fehler beim Laden des Schülers:', error);
            }
        }

        document.addEventListener('DOMContentLoaded', async () => {
            const params = new URLSearchParams(window.location.search);
            const studentId = params.get('schueler_id');

            if (studentId) {
                await loadStudent(studentId);
            } else {
                await loadClasses();
            }
        });
    </script>



<?php
require_once __DIR__ . '/../../new/layout/html_bottom.php';
?>