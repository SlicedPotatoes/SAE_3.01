<?php
$TEST = true;// À mettre sûr true pendant un test unitaire
// établit une variable connexion qui fait la liaison avec la base de données
if($TEST){
    echo "Base de données de test";
    $host = "localhost";
    $user = "postgres";
    $password = "12345";
    $dbname = "postgres";
}else{
    $host = "tommytech.net";
    $user = "kevin";
    $password = "patate360";
    $dbname = "postgres";
}
try {
    $connection = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}