<?php
/*
 * Script de connexion
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require "../Model/Account.php";
require "../Model/Student.php";

// Compte "Hard codé"
$account = Account::getAllAccount();
session_start();

var_dump($account);
var_dump($_POST['id']);

if(isset($_POST['id'])) {
    var_dump($account[$_POST['id']]);

    $_SESSION['role'] = $account[$_POST['id']]->getAccountType();
    $_SESSION['account'] = $account[$_POST['id']];

    if($_SESSION['role'] == AccountType::Student) {
        $_SESSION['account'] = Student::getStudentByIdAccount($_POST['id']);
    }
}

header("Location: ../index.php");