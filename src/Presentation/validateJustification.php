<?php
/*
Ce fichier gère la validation des justificatifs par le responsable pédagogique.
Il permet de valider ou refuser les absences liées à un justificatif, d'enregistrer le motif de refus si nécessaire,
et de changer l'état du justificatif vers "Traité".
*/

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../Presentation/globalVariable.php";
require_once "../../vendor/autoload.php";

use Uphf\GestionAbsence\Model\Account\AccountType;
use Uphf\GestionAbsence\Model\Account\Account;
use Uphf\GestionAbsence\Model\Connection;
use Uphf\GestionAbsence\Model\Justification\Justification;
use Uphf\GestionAbsence\Model\Justification\StateJustif;

session_start();


global $PROD;

// Vérifier que la requête est bien POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $errorMessage = "Erreur 403 - Accès refusé";
    if (!$PROD) { $errorMessage = $errorMessage.": Seulement les requêtes POST sont autorisées"; }
    header('Location: ../index.php?errorMessage[]='.urlencode($errorMessage));
    exit;
}

// Vérifier que les champs requis sont présents
if (!isset($_POST['idJustification'])) {
    $errorMessage = "Erreur 400 - Requête invalide";
    if (!$PROD) { $errorMessage = $errorMessage.": La demande est incorrecte. Vérifiez les champs et réessayer."; }
    header('Location: ../index.php?errorMessage[]='.urlencode($errorMessage));
    exit;
}

// Récupération des données du formulaire
$idJustification = $_POST['idJustification'];
$rejectionReason = trim($_POST['rejectionReason'] ?? '');
$absences = $_POST['absences'] ?? [];

try {
    // Récupérer le justificatif
    $justification = Justification::getJustificationById($idJustification);
    
    // Vérifier que le justificatif existe et est en attente de traitement
    if (!$justification) {
        $errorMessage = "Erreur 404 - Introuvable";
        if (!$PROD) { $errorMessage = $errorMessage.": Impossible de trouver la ressource demandée."; }
        header('Location: ../index.php?errorMessage[]='.urlencode($errorMessage));
        exit;
    }

    if ($justification->getCurrentState() !== StateJustif::NotProcessed) {
        $errorMessage = "Erreur 400 - Requête invalide";
        if (!$PROD) { $errorMessage = $errorMessage.": La demande est incorrecte. Le justificatif a déjà été traité"; }
        header('Location: ../index.php?errorMessage[]='.urlencode($errorMessage));
        exit;
    }
    
    // Traiter chaque absence
    $hasRefused = false;
    foreach ($absences as $key => $state) {
        // Format de la clé: "idStudent_time"
        list($idStudent, $time) = explode('_', $key, 2);
        
        // Convertir le state en valeur de l'enum
        $stateValue = ucfirst($state); // 'validated' -> 'Validated', 'refused' -> 'Refused'
        
        if ($stateValue === 'Refused') {
            $hasRefused = true;
        }
        
        // Mettre à jour l'état de l'absence
        $query = "UPDATE absence SET currentState = :state WHERE idStudent = :idStudent AND time = :time";
        $connection = Connection::getInstance();
        $stmt = $connection->prepare($query);
        $stmt->execute([
            ':state' => $stateValue,
            ':idStudent' => $idStudent,
            ':time' => $time
        ]);
    }
    
    // Si des absences ont été refusées, enregistrer le motif de refus
    if ($hasRefused && !empty($rejectionReason)) {
        $justification->setRefusalReason($rejectionReason);
    }
    
    // Changer l'état du justificatif vers "Traité"
    $justification->changeStateJustification();
    
    // Redirection avec message de succès
    $successMessage = "Justificatif traité avec succès";
    header('Location: ../index.php?successMessage[]='.urlencode($successMessage));
    exit;
    
} catch (Exception $e) {
    // En cas d'erreur
    $errorMessage = "Erreur 500 - Erreur interne";
    if (!$PROD) { 
        $errorMessage = $errorMessage.": ".$e->getMessage();
        error_log("Erreur lors de la validation du justificatif : " . $e->getMessage());
    }
    header('Location: ../index.php?errorMessage[]='.urlencode($errorMessage));
    exit;
}
