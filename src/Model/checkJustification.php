<?php
// Script de test CLI pour vérifier la connexion et la récupération des justificatifs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../Presentation/globalVariable.php';
require_once __DIR__ . '/../Model/connection.php';
require_once __DIR__ . '/../Model/Justification/Justification.php';

$studentId = $argv[1] ?? null;
if ($studentId === null) {
    echo "Usage: php check_justifications.php <studentId>\n";
    echo "Exemple: php check_justifications.php 1\n";
    exit(1);
}

echo "Test connection et récupération des justificatifs pour studentId = $studentId\n";

if (isset($connection) && $connection instanceof PDO) {
    echo "Connection OK (PDO).\n";
} else {
    echo "Connection NON initialisée. Vérifie src/Model/connection.php et les credentials.\n";
    exit(2);
}

try {
    $list = Justification::selectJustification($studentId, null, null, null, false);
    echo "Nombre de justificatifs récupérés: " . count($list) . "\n";
    if (count($list) > 0) {
        $j = $list[0];
        echo "Exemple (premier justificatif):\n";
        echo "  id: " . $j->getIdJustification() . "\n";
        echo "  cause: " . $j->getCause() . "\n";
        echo "  startDate: " . ($j->getStartDate() instanceof DateTime ? $j->getStartDate()->format('c') : 'NULL') . "\n";
        echo "  endDate: " . ($j->getEndDate() instanceof DateTime ? $j->getEndDate()->format('c') : 'NULL') . "\n";
        echo "  fichiers (getFiles):\n";
        $files = $j->getFiles();
        foreach ($files as $f) {
            if (is_object($f) && method_exists($f, 'getFileName')) {
                echo "    - " . $f->getFileName() . "\n";
            } elseif (is_array($f)) {
                echo "    - (array) " . json_encode($f) . "\n";
            } else {
                echo "    - " . (string)$f . "\n";
            }
        }
    }
} catch (Exception $e) {
    echo "Exception lors de la récupération: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(3);
}

echo "Test terminé.\n";

