<?php
// php -S localhost:8080 (zB)
// In Datenbank schreiben
include dirname(__FILE__) . '/../helpers/service/all.php';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
  header('Location: /');
  die();
}



// $schulerService = new SchuelerService();

$schuelrList = [
  ['name' => 'Elisa'],
  ['name' => 'David'],
  ['name' => 'Nikolas'],
];

include dirname(__FILE__) . '/index.view.php';
?>