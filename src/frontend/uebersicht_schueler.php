<?php
require_once __DIR__ . '/layout/html_top.php';
require_once __DIR__ . '/../backend/db.php';

$query = "
    SELECT
        s.schueler_id,
        s.vorname,
        s.nachname,
        s.geburtsdatum,
        k.klasse_id,
        k.bezeichnung AS klasse_name,
        k.schuljahr
    FROM schueler s
    JOIN klasse k ON s.klasse_id = k.klasse_id
    ORDER BY k.bezeichnung, s.nachname, s.vorname
";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p>Fehler beim Laden der Schülerdaten: " . htmlspecialchars($e->getMessage()) . "</p>";
    $students = [];
}

$classQuery = "SELECT klasse_id, bezeichnung, schuljahr FROM klasse ORDER BY bezeichnung";
try {
    $classStmt = $pdo->prepare($classQuery);
    $classStmt->execute();
    $classes = $classStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p>Fehler beim Laden der Klassen: " . htmlspecialchars($e->getMessage()) . "</p>";
    $classes = [];
}
?>

<div class="container">
    <h1>Übersicht Schüler</h1>
    <div class="filter-row">
        <label for="classFilter">Klasse filtern:</label>
        <select id="classFilter" onchange="filterStudentsByClass()">
            <option value="">Alle Klassen</option>
            <?php foreach ($classes as $class): ?>
                <option value="<?php echo htmlspecialchars($class['klasse_id']); ?>"><?php echo htmlspecialchars($class['bezeichnung'] . ' (' . $class['schuljahr'] . ')'); ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <?php if (!empty($students)): ?>
        <table class="data-table">
            <thead>
                <tr style="background-color: #4CAF50; color: white;">
                    <th>Vorname</th>
                    <th>Nachname</th>
                    <th>Geburtsdatum</th>
                    <th>Klasse</th>
                    <th>Schuljahr</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                    <tr data-klasse-id="<?php echo htmlspecialchars($student['klasse_id']); ?>">
                        <td><?php echo htmlspecialchars($student['vorname']); ?></td>
                        <td><?php echo htmlspecialchars($student['nachname']); ?></td>
                        <td><?php echo htmlspecialchars($student['geburtsdatum'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($student['klasse_name']); ?></td>
                        <td><?php echo htmlspecialchars($student['schuljahr']); ?></td>
                        <td>
                            <a href="./schueler_erstellen.php?schueler_id=<?php echo htmlspecialchars($student['schueler_id']); ?>">Bearbeiten</a>
                        </td>
                        <td>
                            <a href="#" class="delete-link" onclick="return submitDeleteStudent(<?php echo htmlspecialchars($student['schueler_id']); ?>);">Löschen</a>
                            <form id="delete-form-<?php echo htmlspecialchars($student['schueler_id']); ?>" method="post" action="../backend/main.php" style="display:none;">
                                <input type="hidden" name="type" value="removeStudent">
                                <input type="hidden" name="schueler_id" value="<?php echo htmlspecialchars($student['schueler_id']); ?>">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p id="studentCount" style="margin-top: 20px;"><strong>Gesamtanzahl Schüler:</strong> <?php echo count($students); ?></p>
    <?php else: ?>
        <p>Keine Schüler gefunden.</p>
    <?php endif; ?>

    <br>
    <button type="button" onclick="window.location.href='./Startseite.php'">Zurück</button>
</div>

<script>
    const urlParams = new URLSearchParams(window.location.search);
    const success = urlParams.get('success');
    if (success) {
        let message = '';
        if (success === '6') {
            message = 'Schüler gelöscht';
        }
        if (message) {
            showToast(message);
            window.history.replaceState(null, null, window.location.pathname);
        }
    }

    function submitDeleteStudent(studentId) {
        if (!confirm('Diesen Schüler wirklich löschen?')) {
            return false;
        }
        const form = document.getElementById(`delete-form-${studentId}`);
        if (form) {
            form.submit();
        }
        return false;
    }

    function filterStudentsByClass() {
        const selectedClass = document.getElementById('classFilter').value;
        const rows = document.querySelectorAll('tbody tr[data-klasse-id]');
        let visibleCount = 0;

        rows.forEach(row => {
            if (!selectedClass || row.getAttribute('data-klasse-id') === selectedClass) {
                row.style.display = '';
                visibleCount += 1;
            } else {
                row.style.display = 'none';
            }
        });

        const countElement = document.getElementById('studentCount');
        if (countElement) {
            countElement.innerHTML = `<strong>Gesamtanzahl Schüler:</strong> ${visibleCount}`;
        }
    }

    function showToast(message) {
        const toast = document.createElement('div');
        toast.textContent = message;
        toast.style.position = 'fixed';
        toast.style.bottom = '20px';
        toast.style.right = '20px';
        toast.style.backgroundColor = '#4CAF50';
        toast.style.color = 'white';
        toast.style.padding = '10px 20px';
        toast.style.borderRadius = '5px';
        toast.style.zIndex = '1000';
        document.body.appendChild(toast);
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 3000);
    }
</script>

<?php
require_once __DIR__ . '/layout/html_bottom.php';
?>