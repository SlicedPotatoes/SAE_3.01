<?php
/**
 * Font end du dashboard d'un étudiant
 */

require_once __DIR__ . "/../../Model/Absence/Absence.php";
require_once __DIR__ . "/../../Model/Justification/Justification.php";

require_once __DIR__ . '/../../Model/Filter/FilterJustification.php';
require_once __DIR__ . '/../../Model/Filter/FilterAbsence.php';

require_once __DIR__ . "/../../Presentation/StudentPresentation.php";
require_once __DIR__ . "/../../Presentation/AbsencePresentation.php";
require_once __DIR__ . "/../../Presentation/JustificationPresentation.php";

require_once __DIR__ . "/../Headers/studentDashboardHeader.php";
require_once __DIR__ . "/../Modal/modalJustificationAbsence.php";

// Récupérer l'onglet courant, pour pouvoir ouvrir la page sur le bon onglet
$currTab = isset($_GET['currTab']) ? $_GET['currTab'] : 'proof';

/*
 * Tableau contenant les filtres
 * Utiliser pour la requête des filtres
 * Et également pour afficher la valeur du filtre actuel
 */

$filter = [
    'proof' => new FilterJustification(
            isset($_GET['proofDateStart']) && $_GET['proofDateStart'] != '' ? $_GET['proofDateStart'] : null,
            isset($_GET['proofDateEnd']) && $_GET['proofDateEnd'] != '' ? $_GET['proofDateEnd'] : null,
            isset($_GET['proofState']) && $_GET['proofState'] != '' ? $_GET['proofState'] : null,
            isset($_GET['proofExam']) && $_GET['proofExam'] == 'on'
    ),
    'abs' => new FilterAbsence(
            isset($_GET['absDateStart']) && $_GET['absDateStart'] != '' ? $_GET['absDateStart'] : null,
            isset($_GET['absDateEnd']) && $_GET['absDateEnd'] != '' ? $_GET['absDateEnd'] : null,
            isset($_GET['absState']) && $_GET['absState'] != '' ? $_GET['absState'] : null,
            isset($_GET['absExam']) && $_GET['absExam'] == 'on',
            isset($_GET['absLocked']) && $_GET['absLocked'] == 'on'
    )
];

?>

<div class="card p-3">
    <?php
    if ($_SESSION["role"] == AccountType::Student)
    {
        require __DIR__ . "/Student/justificationButton.html";
    }

    require __DIR__ . "/tabAbsencesJustificatifs.php";

    ?>
</div>
