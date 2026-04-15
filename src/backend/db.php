<?php

  error_reporting(E_ALL);
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);

  $host = "db";
  $port = 3306;
  $dbname = "schulverwaltung";
  $user = 'devuser';
  $pass = 'devpass';

  try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8;";
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } catch (PDOException $e) {
    echo "<p> Verbindungsfehler... " . $e->getMessage() . "</p>";
  }

?>