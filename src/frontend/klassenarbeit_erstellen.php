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

            <button type="submit">Speichern</button>
            <br>
            <button type="button" onclick="window.location.href='./Startseite.php'" style="margin-top: 2%;">Zurück</button>
        </form>
    </div>


<?php
require_once __DIR__ . '/../../new/layout/html_bottom.php';
?>