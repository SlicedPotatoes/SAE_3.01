<?php
/**
 * Font end du dashboard d'un étudiant
 */

use Uphf\GestionAbsence\Model\Account\AccountType;
use Uphf\GestionAbsence\Model\Absence\StateAbs;
use Uphf\GestionAbsence\Model\Absence\Absence;
use Uphf\GestionAbsence\Model\Justification\Justification;

use Uphf\GestionAbsence\Model\Filter\FilterJustification;
use Uphf\GestionAbsence\Model\Filter\FilterAbsence;

use Uphf\GestionAbsence\Presentation\StudentPresentation;
use Uphf\GestionAbsence\Presentation\AbsencePresentation;
use Uphf\GestionAbsence\Presentation\JustificationPresentation;

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

<?php require __DIR__ . "/Composants/Headers/studentProfileHeader.php"; ?>

<div class="card p-3 flex-fill d-flex flex-column" style="min-height: 0">
    <?php require __DIR__ . "/Composants/tabsStudentProfile.php"; ?>
</div>
