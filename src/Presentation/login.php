<?php
/**
 * Script de gestion de connexion
 *
 * Vérification des identifiants fournis par l'utilisateur
 *
 * Si les identifiants fournis sont correcte, création d'une session avec les éléments nécéssaire au bon fonctionnement de l'application.
 *
 * TODO: Connecter a la BDD
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../../vendor/autoload.php";

use Uphf\GestionAbsence\Model\Account\Student;
use Uphf\GestionAbsence\Model\Account\Account;
use Uphf\GestionAbsence\Model\Account\AccountType;


// Compte "Hard codé"
$account = Account::getAllAccount();
session_start();

var_dump($account);
var_dump($_POST['id']);

if(isset($_POST['id'])) {
    var_dump($account[$_POST['id']]);

    $_SESSION['role'] = $account[$_POST['id']]->getAccountType();
    $_SESSION['account'] = $account[$_POST['id']];

    if($_SESSION['role'] == AccountType::Student)
    {
        $_SESSION['account'] = Student::getStudentByIdAccount($_POST['id']);
    }
}

header("Location: ../index.php");