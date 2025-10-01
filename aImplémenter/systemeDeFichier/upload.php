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
    // Création automatique du dossier C:\upload si il n'existe pas
    if (!mkdir($uploadDir, 0777, true)) {
        http_response_code(500);
        exit("Le dossier d'upload n'existe pas et n'a pas pu être créé: $uploadDir");
    }
}
if (!is_writable($uploadDir)) {
    http_response_code(500);
    exit("Le dossier n'est pas accessible en écriture: $uploadDir");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(200);
    exit("Uploader prêt. Envoyez un fichier via POST multipart/form-data sous le champ 'files[]'.");
}
if (!isset($_FILES['files'])) {
    http_response_code(400);
    exit("Aucun fichier reçu (champ 'files[]' manquant).");
}

// Gestion de plusieurs fichiers
$responses = [];
$files = $_FILES['files'];
$fileCount = is_array($files['name']) ? count($files['name']) : 0;

for ($i = 0; $i < $fileCount; $i++) {
    $err = $files['error'][$i];
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
        $responses[] = [
            'ok' => false,
            'message' => "Erreur upload: " . ($errors[$err] ?? "Inconnue"),
            'index' => $i
        ];
        continue;
    }

    // Limite de taille (5 Mo)
    if ($files['size'][$i] > 5 * 1024 * 1024) {
        $responses[] = [
            'ok' => false,
            'message' => "Fichier trop volumineux (5Mo max).",
            'index' => $i
        ];
        continue;
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
        $mime  = @$finfo->file($files['tmp_name'][$i]) ?: null;
    } elseif (function_exists('mime_content_type')) {
        $mime = @mime_content_type($files['tmp_name'][$i]) ?: null;
    }

    // Extension fournie par le nom du fichier (repli)
    $ext = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
    if ($ext === 'jpeg') { $ext = 'jpg'; }

    if ($mime !== null) {
        if (!isset($allowedMimeToExt[$mime])) {
            $responses[] = [
                'ok' => false,
                'message' => "Format de fichier non autorisé.",
                'index' => $i
            ];
            continue;
        }
        $ext = $allowedMimeToExt[$mime];
    } else {
        if (!in_array($ext, $allowedExtensions, true)) {
            $responses[] = [
                'ok' => false,
                'message' => "Format de fichier non autorisé.",
                'index' => $i
            ];
            continue;
        }
    }

    // Nom de fichier sûr + unique
    $base = pathinfo($files['name'][$i], PATHINFO_FILENAME);
    $base = preg_replace('/[^A-Za-z0-9._-]/', '_', $base);
    $filename = $base . '_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;

    $targetPath = $uploadDir . $filename;

    if (!is_uploaded_file($files['tmp_name'][$i])) {
        $responses[] = [
            'ok' => false,
            'message' => "Fichier non valide.",
            'index' => $i
        ];
        continue;
    }

    if (move_uploaded_file($files['tmp_name'][$i], $targetPath)) {
        @chmod($targetPath, 0644);
        $responses[] = [
            'ok' => true,
            'message' => 'Fichier uploadé avec succès',
            'filename' => $filename,
            'ext' => $ext,
            'index' => $i
        ];
    } else {
        $responses[] = [
            'ok' => false,
            'message' => "Erreur lors de l’upload (droits/chemin).",
            'index' => $i
        ];
    }
}

// Réponse JSON globale
header('Content-Type: application/json; charset=UTF-8');
echo json_encode($responses);

