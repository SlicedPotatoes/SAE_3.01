<?php

/**
 * Script permettant de récupérer un fichier upload
 *
 * Le script vérifie si l'utilisateur a l'autorisation de visionner le fichier
 */

require_once __DIR__ . "/../vendor/autoload.php";

use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\DB\Select\JustificationSelector;
use Uphf\GestionAbsence\Model\DB\Select\TableSelector;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\Model\GlobalVariable;
use Uphf\GestionAbsence\Model\Hydrator\JustificationHydrator;

AuthManager::init();

if(!GlobalVariable::PROD()) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

// Vérifier si le fichier a visionné a été fournis
if($_SERVER["REQUEST_METHOD"] !== "GET" || !isset($_GET['idFile'])) {
    http_response_code(400);
    echo "Aucun fichier spécifié";
    exit();
}

// Récupération du fichier dans la BDD
$file = TableSelector::fromTableWhere("file", ['idfile'], [[$_GET['idFile'], PDO::PARAM_INT]]);

// Le fichier n'existe pas dans la BDD
if(count($file) === 0) {
    http_response_code(404);
    echo "Fichier non trouvé";
    exit();
}

$file = JustificationHydrator::unserializeFile($file[0]);
$userAllowedToSee = false;

// Autoriser seulement le RP et l'étudiant dans le cas ou c'est un fichier qu'il a upload, d'accéder au fichier
if(AuthManager::isRole(AccountType::EducationalManager)) {
    $userAllowedToSee = true;
}
else if(AuthManager::isRole(AccountType::Student)) {
    $justification = JustificationSelector::getJustificationById($file->getJustification());
    if(AuthManager::getAccount()->getIdAccount() === $justification->getStudent()->getIdAccount()) {
        $userAllowedToSee = true;
    }
}

// Si l'utilisateur n'a pas le droit de voir le fichier
if(!$userAllowedToSee) {
    http_response_code(403);
    echo "Accès refusé";
    exit();
}

// Récupérer le path du fichier
$filePath =  dirname(__DIR__) . DIRECTORY_SEPARATOR . "upload" . DIRECTORY_SEPARATOR . $file->getFileName();

if(!file_exists($filePath)) {
    http_response_code(404);
    echo "Fichier non trouvé";
    exit();
}

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $finfo->file($filePath);

// Différent header pour que le navigateur sache comment afficher le fichier
header('Content-Type: ' . $mimeType);
header('Content-Disposition: inline; filename="' . $file->getFileName() . '"');
header('Content-Length: ' . filesize($filePath));
// Demander au navigateur de ne pas mettre en cache pour éviter le risque sur un poste partagé
// qu'un utilisateur accéde a la version en cache alors qu'il n'y est pas autorisé.
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

readfile($filePath);
exit();