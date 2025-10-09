<?php
// researchFile.php: sert les fichiers uploadés de manière contrôlée
// Usage: researchFile.php?name=<filename>

ini_set('display_errors', 0);
error_reporting(0);

// Déterminer le dossier selon l'OS
if (stripos(PHP_OS_FAMILY, 'Windows') !== false) {
    $baseDir = __DIR__ . DIRECTORY_SEPARATOR . 'merguez' . DIRECTORY_SEPARATOR;
} else {
    $baseDir = '/merguez/';
}

// Vérifier que le dossier d'upload existe
if (!is_dir($baseDir)) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');
    exit("Le dossier d'upload n'existe pas: $baseDir");
}

if (!isset($_GET['name']) || $_GET['name'] === '') {
    http_response_code(400);
    header('Content-Type: text/plain; charset=UTF-8');
    exit('Paramètre name manquant');
}

$name = $_GET['name'];
// Sécurisation du nom: on n'autorise que les patterns sûrs générés par upload.php
if (!preg_match('/^[A-Za-z0-9._-]+$/', $name)) {
    http_response_code(400);
    header('Content-Type: text/plain; charset=UTF-8');
    exit('Nom de fichier invalide');
}

$path = $baseDir . $name;
if (!is_file($path)) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=UTF-8');
    exit('Fichier introuvable');
}

// Détecter le MIME
$mime = 'application/octet-stream';
if (class_exists('finfo')) {
    $f = new finfo(FILEINFO_MIME_TYPE);
    $mime = $f->file($path) ?: $mime;
} elseif (function_exists('mime_content_type')) {
    $mime = mime_content_type($path) ?: $mime;
}

// Choisir la disposition (inline ou attachment). dl=1 force le téléchargement.
$forceDownload = isset($_GET['dl']) && ($_GET['dl'] === '1' || strtolower($_GET['dl']) === 'true');
// Affichage inline pour images/PDF si pas de forcing
$inlineTypes = ['image/jpeg','image/png','application/pdf'];
$disposition = $forceDownload ? 'attachment' : (in_array($mime, $inlineTypes, true) ? 'inline' : 'attachment');

// En-têtes de téléchargement/affichage robustes
$size = filesize($path);
header('Content-Type: ' . $mime);
header('Content-Length: ' . $size);
header('Content-Disposition: ' . $disposition . '; filename="' . basename($name) . '"');
header('Content-Transfer-Encoding: binary');
header('Accept-Ranges: bytes');
header('Cache-Control: private, no-transform');

// Ne pas envoyer de corps pour une requête HEAD
if (strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'HEAD') {
    exit;
}

// Nettoyer les buffers éventuels avant l'envoi
if (function_exists('ob_get_level')) {
    while (ob_get_level() > 0) { @ob_end_clean(); }
}
@flush();

$fp = fopen($path, 'rb');
if ($fp) {
    while (!feof($fp)) {
        echo fread($fp, 8192);
    }
    fclose($fp);
}
exit;
