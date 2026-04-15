<?php
// @TODO Aufrufschutz implementieren
include dirname(__FILE__) . '/student.php';
$db = require(dirname(__FILE__) . '/../db.php');

$schuelerService = new SchuelerService($db);
$schulklasseService = new SchuelerService($db);



define('ABC', 'DEF');