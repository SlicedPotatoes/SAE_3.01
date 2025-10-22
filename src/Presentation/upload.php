<?php
/*
Ce fichier gère l'upload des justificatifs d'absence pour les étudiants.
Il permet d'envoyer un justificatif (formats acceptés : jpeg, jpg, png, pdf) dans un dossier spécifique,
de vérifier la validité des fichiers et des données associées (dates, motif),
d'enregistrer les informations en base de données,
et d'envoyer une notification par mail à l'étudiant après traitement.
*/

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../Presentation/globalVariable.php";
require_once "../Model/Account/Student.php";
require_once "../Model/Justification/Justification.php";
require_once "../Model/mail/mailAccRecpJusti.php";
require_once "../Model/Account/AccountType.php";

global $PROD, $LIMIT_FILE_SIZE_UPLOAD, $ALLOWED_MIME_TYPE, $ALLOWED_EXTENSIONS_FILE;

/*var_dump($_POST);
var_dump($_FILES);*/

session_start();

// Sélection du dossier d'upload selon l'OS (dev Windows, prod Linux)
if (stripos(PHP_OS_FAMILY, 'Windows') !== false) {
    // Dev local: dossier "upload" dans le projet
    $uploadDir = 'C:\upload\\';
}
else {
    // Prod Linux : dossier racine
    $uploadDir = '/var/www/upload/';
}

if (!is_dir($uploadDir)) {
    // Création automatique du dossier d'upload (normalement utile qu'en phase de dev)
    if (!mkdir($uploadDir, 0777, true)) {
        $errorMessage = "HTTP 500 Internal Server Error";
        if (!$PROD) { $errorMessage = $errorMessage.": Le dossier d'upload n'existe pas et n'a pas pu être créé: $uploadDir"; }
        header('Location: ../index.php?errorMessage[]='.urlencode($errorMessage));
        exit;
    }
}
// Le dossier d'upload n'est pas accessible en écriture
if (!is_writable($uploadDir)) {
    $errorMessage = "HTTP 500 Internal Server Error";
    if (!$PROD) { $errorMessage = $errorMessage.": Le dossier n'est pas accessible en écriture: $uploadDir"; }
    header('Location: ../index.php?errorMessage[]='.urlencode($errorMessage));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $errorMessage = "HTTP 405 Method Not Allowed";
    if (!$PROD) { $errorMessage = $errorMessage.": Seulement les requêtes POST sont autorisées"; }
    header('Location: ../index.php?errorMessage[]='.urlencode($errorMessage));
    exit;
}
if (!isset($_FILES['files']) || !isset($_POST['startDate']) || !isset($_POST['endDate']) || !isset($_POST['absenceReason'])) {
    $errorMessage = "HTTP 400 Bad Request";
    if (!$PROD) { $errorMessage = $errorMessage.": Un des champs obligatoires n'a pas été envoyé"; }
    header('Location: ../index.php?errorMessage[]='.urlencode($errorMessage));
    exit;
}

if(!isset($_SESSION['role']) || $_SESSION['role'] != AccountType::Student) {
    $errorMessage = "HTTP 400 Bad Request";
    if (!$PROD) { $errorMessage = $errorMessage.": Un compte étudiant est nécessaire"; }
    header('Location: ../index.php?errorMessage[]='.urlencode($errorMessage));
    exit;
}

// Récupération des données du form
$absenceReason = $_POST['absenceReason'];
$startDate = $_POST['startDate'];
$endDate =  $_POST['endDate'];
$files = $_FILES['files'];

// Vérifier que la dateDeDebut est inférieur ou égale à la dateDeFin
if(DateTime::createFromFormat("Y-m-d", $startDate) > DateTime::createFromFormat("Y-m-d", $endDate)) {
    $errorMessage = "HTTP 400 Bad Request: La date de début doit être inférieur ou égal a la date de fin";
    header('Location: ../index.php?errorMessage[]='.urlencode($errorMessage));
    exit;
}

// Liste des erreurs
$errors = [];
if(!$PROD) {
    $errors = [
        UPLOAD_ERR_INI_SIZE   => "Dépasse upload_max_filesize",
        UPLOAD_ERR_FORM_SIZE  => "Dépasse MAX_FILE_SIZE du formulaire",
        UPLOAD_ERR_PARTIAL    => "Upload partiel",
        UPLOAD_ERR_NO_FILE    => "Aucun fichier fourni",
        UPLOAD_ERR_NO_TMP_DIR => "Dossier temporaire manquant",
        UPLOAD_ERR_CANT_WRITE => "Échec d'écriture disque",
        UPLOAD_ERR_EXTENSION  => "Extension PHP a stoppé l'upload",
    ];
}
else {
    $errors = [
        UPLOAD_ERR_INI_SIZE   => "Dépasse de la taille maximale",
        UPLOAD_ERR_FORM_SIZE  => "Dépasse de la taille maximale",
    ];
}

$warningMessages = [];
$filesNameForDB = [];

for($i = 0; $i < count($files['name']); $i++) {
    $file = [
        "name" => $files["name"][$i],
        "full_path" => $files["full_path"][$i],
        "type" => $files["type"][$i],
        "tmp_name" => $files["tmp_name"][$i],
        "error" => $files["error"][$i],
        "size" => $files["size"][$i]
    ];

    //var_dump($file);

    // Vérification des erreurs d'upload
    $err = $file['error'];
    if ($err !== UPLOAD_ERR_OK) {
        if(array_key_exists($err, $errors)) {
            $warningMessages[] = urlencode($file['name'].': '.$errors[$err]);
        }
        continue;
    }

    // Vérification de la taille du fichier
    if($file['size'] > $LIMIT_FILE_SIZE_UPLOAD) {
        $warningMessages[] = urlencode($file['name'].': Dépasse de la taille maximale');
        continue;
    }

    // Vérification de si le fichier a été upload dans les fichiers temporaires
    if (!is_uploaded_file($file['tmp_name'])) {
        if(!$PROD) { $warningMessages[] = urlencode($file['name'].': Upload Error'); }
        else { $warningMessages[] = urlencode($file['name'].': Erreur lors de la réception du fichier'); }
        continue;
    }

    // Vérification du MIME Type du fichier

    $mime = null;
    if (class_exists('finfo')) {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime  = @$finfo->file($file['tmp_name']) ?: null;
    } elseif (function_exists('mime_content_type')) {
        $mime = @mime_content_type($file['tmp_name']) ?: null;
    }

    if($mime == null || !in_array($mime, $ALLOWED_MIME_TYPE) ) {
        if(!$PROD) { $warningMessages[] = urlencode($file['name'].': MIME Type Not Allowed '.$mime); }
        else { $warningMessages[] = urlencode($file['name'].': Format de fichier non autorisé.'); }
        continue;
    }

    // Vérification de l'extension du fichier
    $ext = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
    if($ext == null || !in_array($ext, $ALLOWED_EXTENSIONS_FILE)) {
        if(!$PROD) { $warningMessages[] = urlencode($file['name'].': File Extension Not Allowed '.$ext); }
        else { $warningMessages[] = urlencode($file['name'].': Format de fichier non autorisé.'); }
        continue;
    }

    // Nom de fichier sûr + unique
    $base = pathinfo($file['name'], PATHINFO_FILENAME);
    $base = preg_replace('/[^A-Za-z0-9._-]/', '_', $base);
    $filename = $base . '_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;

    $targetPath = $uploadDir.$filename;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        chmod($targetPath, 0644);
        $filesNameForDB[] = $filename;
    }
    else {
        if(!$PROD) { $warningMessages[] = urlencode($file['name'].': Move in uploadDirectory error'); }
        else { $warningMessages[] = urlencode($file['name'].': Erreur lors de la réception du fichier'); }
    }
}

// Ajout dans la BDD
if(!Justification::insertJustification($_SESSION['account']->getIdAccount(), $absenceReason, $startDate, $endDate, $filesNameForDB)) {
    // TODO: Supprimer les fichiers qui ont été upload
    $errorMessage = "HTTP 400 Bad Request: Pas d'absence sur la période sélectionnée";
    header('Location: ../index.php?errorMessage[]='.urlencode($errorMessage));
    exit;
}

mailAccRecpJusti(
    $_SESSION['account']->getLastName(),
    $_SESSION['account']->getFirstName(),
    $_SESSION['account']->getEmail(),
    DateTime::createFromFormat("Y-m-d", $startDate)->format("d/m/Y"),
    DateTime::createFromFormat("Y-m-d", $endDate)->format("d/m/Y")
);

$successParameter = 'successMessage[]='.urlencode("Justificatif envoyé avec succès");
$warningParameter = "";

if(count($warningMessages) != 0) {
    $warningParameter = "&warningMessage[]=". implode("&warningMessage[]=", $warningMessages);
}

header('Location: ../index.php?'.$successParameter.$warningParameter);