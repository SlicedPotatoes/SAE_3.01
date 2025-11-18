<?php
/**
 * Script exécuté à la mise à jour de composer
 * Permet de déplacer le script js, le css et les fonts récupérés par composer et les mettres dans public
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$files = [
    'vendor/twbs/bootstrap/dist/css/bootstrap.min.css' => 'public/style/bootstrap.min.css',
    'vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js' => 'public/script/bootstrap.bundle.min.js',
    'vendor/twbs/bootstrap-icons/font/bootstrap-icons.min.css' => 'public/style/bootstrap-icons.min.css',
    'vendor/twbs/bootstrap-icons/font/fonts/bootstrap-icons.woff' => 'public/style/fonts/bootstrap-icons.woff',
    'vendor/twbs/bootstrap-icons/font/fonts/bootstrap-icons.woff2' => 'public/style/fonts/bootstrap-icons.woff2'
];

mkdir('public/style/fonts');

foreach ($files as $src => $dest) {
    copy($src, $dest);
}
