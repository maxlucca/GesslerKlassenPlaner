<?php
require_once __DIR__ . '/../../new/layout/html_top.php';
?>



<div class="container">
    <h1>Schulverwaltung</h1>
    
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
            <span class="description">Noten & Leistungen eintragen</span>
        </a>

        <a href="uebersicht.php" class="menu-card" style="background-color: #f8fafc;">
            <span class="icon">📊</span>
            <span>Datenbank-Übersicht</span>
            <span class="description">Auswertungen & Listen</span>
        </a>
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
require_once __DIR__ . '/../../new/layout/html_bottom.php';
?>