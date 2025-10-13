<?php
/*
 * Script de connexion
 */

require "../Model/Student.php";

// Compte "Hard codé"
$names = [
    -1 => ["Dimitri", "van Steenkiste", "Dimitri.Vansteenkiste@uphf.fr"],
    -2 => ["Isaac", "Godisiabois", "Isaac.Godisiabois@uphf.fr"],
    -3 => ["Esteban", "Helin", "Esteban.Helin@uphf.fr"],
    -4 => ["Yann", "Dascotte", "Yann.Dascotte@uphf.fr"],
    -5 => ["Kevin", "Masmejean", "Kevin.Masmejean@uphf.fr"],
    -6 => ["Louis", "Picouleau", "Louis.Picouleau@uphf.fr"]
];

session_start();

if(isset($_POST['id'])) {
    $_SESSION['role'] = "student";
    $_SESSION['student'] = new Student($_POST['id'], $names[$_POST['id']][1], $names[$_POST['id']][0], null, $names[$_POST['id']][2], null);
}

header("Location: ../index.php");