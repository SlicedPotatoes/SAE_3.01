<?php
/*
 * View pour l'US : En tant que responsable pédagogique, je veux examiner les pièces justificatives afin d'approuver ou rejeter les justificatifs
 */

use Uphf\GestionAbsence\Model\Filter\FilterJustification;
use Uphf\GestionAbsence\Model\Justification\StateJustif;

// Récupérer l'onglet courant, pour pouvoir ouvrir la page sur le bon onglet
$currTab = isset($_GET['currTab']) ? $_GET['currTab'] : 'proofToDo';

$filter =
    [
        'proofToDo' => new FilterJustification(
            isset($_GET['proofToDoDateStart']) && $_GET['proofToDoDateStart'] != '' ? $_GET['proofToDoDateStart'] : null,
            isset($_GET['proofToDoDateEnd']) && $_GET['proofToDoDateEnd'] != '' ? $_GET['proofToDoDateEnd'] : null,
            StateJustif::NotProcessed->value,
            isset($_GET['proofToDoExam']) && $_GET['proofToDoExam'] == 'on',
            true
        ),
        'proofDone' => new FilterJustification(
            isset($_GET['proofDoneDateStart']) && $_GET['proofDoneDateStart'] != '' ? $_GET['proofDoneDateStart'] : null,
            isset($_GET['proofDoneDateEnd']) && $_GET['proofDoneDateEnd'] != '' ? $_GET['proofDoneDateEnd'] : null,
            StateJustif::Processed->value,
            isset($_GET['proofDoneExam']) && $_GET['proofDoneExam'] == 'on'
        ),
    ];

?>

<?php require __DIR__ . "/Composants/Headers/justificationListHeader.php"; ?>

<div class="card p-3 flex-fill d-flex flex-column" style="min-height: 0">
    <?php require __DIR__ . "/Composants/tabsJustificationList.php"; ?>
</div>