<?php

require "../Model/Student.php";

$names = [
    -1 => ["Dimitri", "van Steenkiste"],
    -2 => ["Isaac", "Godisiabois"],
    -3 => ["Esteban", "Helin"],
    -4 => ["Yann", "Dascotte"],
    -5 => ["Kevin", "Masmejean"],
    -6 => ["Louis", "Picouleau"]
];

session_start();

if(isset($_POST['id'])) {
    $_SESSION['role'] = "student";

    $_SESSION['student'] = new Student($_POST['id'], $names[$_POST['id']][1], $names[$_POST['id']][0], null, null, null);

}

header("Location: ../index.php");