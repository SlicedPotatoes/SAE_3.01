<?php
$host = "tommytech.net";
$user = "kevin";
$password = "patate360";
$dbname = "postgres";
$port = "5432";
try {
    $connection = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo $e->getMessage();
}