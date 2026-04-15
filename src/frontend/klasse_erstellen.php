<?php
require_once __DIR__ . '/../../new/layout/html_top.php';
?> 


    <div class="container">
        <h1>Klasse erstellen</h1>
        <form method="post" action="../backend/main.php">
            <input type="hidden" name="type" value="addClass">
            <div>
                <label for="bezeichnung">Bezeichnung:</label>
                <input type="text" id="bezeichnung" name="bezeichnung" placeholder="z.B. 10a" required>
            </div>
            <div>
                <label for="schuljahr">Schuljahr:</label>
                <input type="text" id="schuljahr" name="schuljahr" placeholder="z.B. 2025/2026">
            </div>
            <button type="submit">Erstellen</button>
            <br>
            <button type="button" onclick="window.location.href='./Startseite.php'" style="margin-top: 2%;">Zurück</button>
        </form>
    </div>


<?php
require_once __DIR__ . '/../../new/layout/html_bottom.php';
?>