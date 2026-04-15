<?php
require_once __DIR__ . '/../../new/layout/html_top.php';
?>

    <div class="container">
        <h1>Klassenarbeit erstellen</h1>
        <form method="post" action="../backend/main.php">
            <input type="hidden" name="type" value="addKlassenarbeit">

            <div>
                <label for="titel">Titel:</label>
                <input type="text" id="titel" name="titel" placeholder="Titel der Klassenarbeit" required>
            </div>

            <div>
                <label for="datum">Datum:</label>
                <input type="date" id="datum" name="datum" required>
            </div>

            <div>
                <label for="gewichtung">Gewichtung:</label>
                <input type="number" id="gewichtung" name="gewichtung" min="0" max="100" placeholder="Gewichtung in %" required>
            </div>

            <div>
                <label for="fach">Fach:</label>
                <select id="fach" name="fach_id" required>
                    <option value="">-- Fach wählen --</option>
                </select>
            </div>

            <div>
                <label for="klasse">Klasse:</label>
                <select id="klasse" name="klasse_id" required>
                    <option value="">-- Klasse wählen --</option>
                </select>
            </div>

            <button type="submit">Speichern</button>
            <br>
            <button type="button" onclick="window.location.href='./Startseite.php'" style="margin-top: 2%;">Zurück</button>
        </form>
    </div>

    <script>
        // Load subjects from backend
        async function loadSubjects() {
            try {
                const response = await fetch('../backend/main.php?type=getSubjects');
                const subjects = await response.json();
                const select = document.getElementById('fach');
                
                subjects.forEach(fach => {
                    const option = document.createElement('option');
                    option.value = fach.fach_id;
                    option.textContent = fach.name;
                    select.appendChild(option);
                });
            } catch (error) {
                console.error('Fehler beim Laden der Fächer:', error);
            }
        }

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
        
        // Load subjects and classes when page loads
        document.addEventListener('DOMContentLoaded', () => {
            loadSubjects();
            loadClasses();
        });
    </script>


<?php
require_once __DIR__ . '/../../new/layout/html_bottom.php';
?>