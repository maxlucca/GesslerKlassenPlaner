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

    <!-- Filter-Formular (Placeholder for future filtering) -->
    <div style="background-color: #f0f0f0; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
        <p><strong>Filter (coming soon):</strong> Hier werden später Filter implementiert</p>
    </div>

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
                </tr>
            </thead>
            <tbody>
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

<?php
require_once __DIR__ . '/../../new/layout/html_bottom.php';
?>