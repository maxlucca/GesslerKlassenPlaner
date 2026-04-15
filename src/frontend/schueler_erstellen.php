<?php
require_once __DIR__ . '/../../new/layout/html_top.php';
?>



    <div class="container">
        <h1>Schüler erstellen</h1>
        <form method="post" action="../backend/main.php">
            <input type="hidden" name="type" value="addStudent">
            
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
            
            <button type="submit">Erstellen</button>
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
        
        // Load classes when page loads
        document.addEventListener('DOMContentLoaded', loadClasses);
    </script>



<?php
require_once __DIR__ . '/../../new/layout/html_bottom.php';
?>