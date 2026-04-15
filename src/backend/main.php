<?php

include('db.php');
include('post-statements.php');
include('get-statements.php');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  postStatements();
} elseif($_SERVER['REQUEST_METHOD'] === 'GET') {
  getStatements();
} else {
  echo "<p> Fehler, bitte überprüfen Sie die Eingabe </p>";
}

?>