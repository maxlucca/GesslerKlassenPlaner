<?php
require_once __DIR__ . '/layout/html_top.php';
?>



<div class="container">
    <h1>Schulverwaltung</h1>
    
    <div class="menu-section">
        <h2>Erstellen & Eintragen</h2>
        <div class="grid">
            <a href="klasse_erstellen.php" class="menu-card">
                <span class="icon">🏫</span>
                <span>Klasse erstellen</span>
                <span class="description">Neue Lerngruppe anlegen</span>
            </a>

            <a href="schueler_erstellen.php" class="menu-card">
                <span class="icon">👤</span>
                <span>Schüler erstellen</span>
                <span class="description">Stammdaten erfassen</span>
            </a>

            <a href="klassenarbeit_erstellen.php" class="menu-card">
                <span class="icon">📝</span>
                <span>Klassenarbeit</span>
                <span class="description">Klassenarbeiten erstellen</span>
            </a>
            <a></a>
            <a href="note_eintragen.php" class="menu-card">
                <span class="icon">✏️</span>
                <span>Note eintragen</span>
                <span class="description">Schülernote einpflegen</span>
            </a>
        </div>
    </div>

    <div class="menu-section">
        <h2>Übersichten</h2>
        <div class="grid">
            <a href="uebersicht.php" class="menu-card" style="background-color: #f8fafc;">
                <span class="icon">📊</span>
                <span>Noten Übersicht</span>
                <span class="description">Auswertungen & Listen</span>
            </a>

            <a href="uebersicht_schueler.php" class="menu-card" style="background-color: #f8fafc;">
                <span class="icon">👥</span>
                <span>Übersicht Schüler</span>
                <span class="description">Schülerstammdaten anzeigen</span>
            </a>
        </div>
    </div>
</div>

<script>
    const urlParams = new URLSearchParams(window.location.search);
    const success = urlParams.get('success');
    if (success) {
        let message = '';
        if (success === '1') message = 'Schüler angelegt';
        else if (success === '2') message = 'Klasse angelegt';
        else if (success === '3') message = 'Klassenarbeit angelegt';
        else if (success === '4') message = 'Note erfolgreich eingetragen';
        else if (success === '5') message = 'Schülerdaten gespeichert';
        if (message) {
            showToast(message);
            // Remove the success parameter from URL to prevent re-showing on refresh
            window.history.replaceState(null, null, window.location.pathname);
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