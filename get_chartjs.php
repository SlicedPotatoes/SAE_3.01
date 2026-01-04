<?php
/**
 * Script exécuté à la mise à jour de composer
 * Permet de télécharger et déplacer le script de ChartJS dans le dossier public
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$url = "https://cdn.jsdelivr.net/npm/chart.js";
$destination = "public/script/chart.js";

$data = file_get_contents($url);
if ($data === false) {
    die("Erreur lors du téléchargement");
}

file_put_contents($destination, $data);
echo "ChartJS : DONE";