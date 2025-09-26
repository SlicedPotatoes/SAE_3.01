<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: text/plain; charset=UTF-8');

// Sélection du dossier d'upload selon l'OS (dev Windows, prod Linux)
if (stripos(PHP_OS_FAMILY, 'Windows') !== false) {
    // Dev local: dossier "upload" dans le projet
    $uploadDir = 'C:\upload';
} else {
    // Prod Linux: dossier racine
    $uploadDir = '/var/www/upload';
}

// Normaliser: s'assurer d'un séparateur final (évite "/var/www/uploadFICHIER")
$uploadDir = rtrim($uploadDir, "\\/") . DIRECTORY_SEPARATOR;

if (!is_dir($uploadDir)) {
    http_response_code(500);
    exit("Le dossier d'upload n'existe pas: $uploadDir");
}
if (!is_writable($uploadDir)) {
    http_response_code(500);
    exit("Le dossier n'est pas accessible en écriture: $uploadDir");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(200);
    exit("Uploader prêt. Envoyez un fichier via POST multipart/form-data sous le champ 'fileToUpload'.");
}
if (!isset($_FILES['fileToUpload'])) {
    http_response_code(400);
    exit("Aucun fichier reçu (champ 'fileToUpload' manquant).");
}

$err = $_FILES['fileToUpload']['error'];
if ($err !== UPLOAD_ERR_OK) {
    $errors = [
        UPLOAD_ERR_INI_SIZE   => "Dépasse upload_max_filesize",
        UPLOAD_ERR_FORM_SIZE  => "Dépasse MAX_FILE_SIZE du formulaire",
        UPLOAD_ERR_PARTIAL    => "Upload partiel",
        UPLOAD_ERR_NO_FILE    => "Aucun fichier fourni",
        UPLOAD_ERR_NO_TMP_DIR => "Dossier temporaire manquant",
        UPLOAD_ERR_CANT_WRITE => "Échec d'écriture disque",
        UPLOAD_ERR_EXTENSION  => "Extension PHP a stoppé l'upload",
    ];
    exit("Erreur upload: " . ($errors[$err] ?? "Inconnue"));
}

// Limite de taille (5 Mo)
if ($_FILES['fileToUpload']['size'] > 5 * 1024 * 1024) {
    exit("Fichier trop volumineux (5Mo max).");
}

// Validation MIME (avec repli si l'extension fileinfo est absente)
$allowedMimeToExt = [
    'image/jpeg'      => 'jpg',
    'image/png'       => 'png',
    'application/pdf' => 'pdf',
];
$allowedExtensions = ['jpg','jpeg','png','pdf'];

$mime = null;
if (class_exists('finfo')) {
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = @$finfo->file($_FILES['fileToUpload']['tmp_name']) ?: null;
} elseif (function_exists('mime_content_type')) {
    $mime = @mime_content_type($_FILES['fileToUpload']['tmp_name']) ?: null;
}

// Extension fournie par le nom du fichier (repli)
$ext = strtolower(pathinfo($_FILES['fileToUpload']['name'], PATHINFO_EXTENSION));
if ($ext === 'jpeg') { $ext = 'jpg'; }

if ($mime !== null) {
    if (!isset($allowedMimeToExt[$mime])) {
        exit("Format de fichier non autorisé.");
    }
    $ext = $allowedMimeToExt[$mime];
} else {
    if (!in_array($ext, $allowedExtensions, true)) {
        exit("Format de fichier non autorisé.");
    }
}

// Nom de fichier sûr + unique
$base = pathinfo($_FILES['fileToUpload']['name'], PATHINFO_FILENAME);
$base = preg_replace('/[^A-Za-z0-9._-]/', '_', $base);
$filename = $base . '_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;

$targetPath = $uploadDir . $filename;

if (!is_uploaded_file($_FILES['fileToUpload']['tmp_name'])) {
    exit("Fichier non valide.");
}

if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $targetPath)) {
    @chmod($targetPath, 0644);
    // Réponse JSON pour permettre l'aperçu côté front
    $resp = [
        'ok' => true,
        'message' => 'Fichier uploadé avec succès',
        'filename' => $filename,
        'ext' => $ext,
    ];
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($resp);
} else {
    http_response_code(500);
    echo "Erreur lors de l’upload (droits/chemin).";
}