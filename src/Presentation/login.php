<?php

require "../Model/Student.php";

$names = [
    -1 => ["Dimitri", "V."],
    -2 => ["Isaac", "G."],
    -3 => ["Esteban", "H."],
    -4 => ["Yann", "D."],
    -5 => ["Kevin", "M."],
    -6 => ["Louis", "P."]
];

session_start();

if(isset($_POST['id'])) {
    $_SESSION['role'] = "student";

    $_SESSION['id'] = $_POST['id'];
    $_SESSION['fName'] = $names[$_POST['id']][0];
    $_SESSION['lName'] = $names[$_POST['id']][1];

}

header("Location: ../index.php");