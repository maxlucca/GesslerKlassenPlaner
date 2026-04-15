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




<?php
require_once __DIR__ . '/../../new/layout/html_bottom.php';
?>