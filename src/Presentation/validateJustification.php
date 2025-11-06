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
    $errorMessage = "HTTP 405 Method Not Allowed";
    if (!$PROD) { $errorMessage = $errorMessage.": Seulement les requêtes POST sont autorisées"; }
    header('Location: ../index.php?errorMessage[]='.urlencode($errorMessage));
    exit;
}

// Vérifier que les champs requis sont présents
if (!isset($_POST['idJustification'])) {
    $errorMessage = "HTTP 400 Bad Request";
    if (!$PROD) { $errorMessage = $errorMessage.": L'ID du justificatif est requis"; }
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
        $errorMessage = "HTTP 404 Not Found";
        if (!$PROD) { $errorMessage = $errorMessage.": Le justificatif n'existe pas"; }
        header('Location: ../index.php?errorMessage[]='.urlencode($errorMessage));
        exit;
    }

    if ($justification->getCurrentState() !== StateJustif::NotProcessed) {
        $errorMessage = "HTTP 400 Bad Request";
        if (!$PROD) { $errorMessage = $errorMessage.": Le justificatif a déjà été traité"; }
        header('Location: ../index.php?errorMessage[]='.urlencode($errorMessage));
        exit;
    }

    foreach ($absences as $key => $values) {
        // Format de la clé: "idStudent_time"
        list($idStudent, $time) = explode('_', $key, 2);

        //$allowedJustification = !($values['state'] == 'Validated') && !($values['lock'] == 'true');
        $allowedJustification = $values['state'] == 'Validated' ? 'false' : ($values['lock'] == 'true' ? 'false' : 'true');

        if($values['state'] == 'Validated') {
            $values['lock'] = true;
        }

        // Mettre à jour l'état de l'absence
        $query = "UPDATE absence SET currentState = :state, allowedJustification = :lock  WHERE idStudent = :idStudent AND time = :time";
        $connection = Connection::getInstance();
        $stmt = $connection->prepare($query);
        $stmt->execute([
            ':state' => $values['state'],
            ':idStudent' => $idStudent,
            ':time' => $time,
            ':lock' => $allowedJustification
        ]);
    }
    
    // Si des absences ont été refusées, enregistrer le motif de refus
    if (!empty($rejectionReason)) {
        $justification->setRefusalReason($rejectionReason);
    }
    
    // Changer l'état du justificatif vers "Traité"
    $justification->processJustification();
    
    // Redirection avec message de succès
    $successMessage = "Justificatif traité avec succès";
    header('Location: ../index.php?successMessage[]='.urlencode($successMessage));
    exit;
    
} catch (Exception $e) {
    // En cas d'erreur
    $errorMessage = "HTTP 500 Internal Server Error";
    if (!$PROD) { 
        $errorMessage = $errorMessage.": ".$e->getMessage();
        error_log("Erreur lors de la validation du justificatif : " . $e->getMessage());
    }
    header('Location: ../index.php?errorMessage[]='.urlencode($errorMessage));
    exit;
}
