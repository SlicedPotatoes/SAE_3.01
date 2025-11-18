<?php
/**
 * Ce script est chargé comme routeur dans la configuration du Built-in Web Server
 *
 * Il permet de rediriger toutes les requêtes vers le fichier index.php
 * Sauf dans le cas ou l'url pointe vers un fichier dans public (notamment les scripts javascript et fichiers css).
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

if ($uri !== '/' && file_exists(__DIR__ . $uri) && !is_dir(__DIR__ . $uri)) {
    return false;
}

require_once __DIR__ . '/index.php';