<?php
/*
 * Font end du dash board
 */
require_once "./Model/Absence.php";
require_once "./Model/Justification.php";

require_once "./View/Modal/modalJustificationAbsence.php";

// Récupérer l'onglet courrant, pour pouvoir ouvrir la page sur le bon onglet
$currTab = isset($_GET['currTab']) ? $_GET['currTab'] : 'proof';

/*
 * Tableau contenant les filtres
 * Utiliser pour la requête des filtres
 * Et egalement pour afficher la valeur du filtre actuel
 */
// Tableau contenant les filtres, utilisé pour effectuer la requete e
$filter = [
    'proof' => [
        'DateStart' => isset($_GET['proofDateStart']) && $_GET['proofDateStart'] != '' ? $_GET['proofDateStart'] : null,
        'DateEnd' => isset($_GET['proofDateEnd']) && $_GET['proofDateEnd'] != '' ? $_GET['proofDateEnd'] : null,
        'State' => isset($_GET['proofState']) && $_GET['proofState'] != '' ? $_GET['proofState'] : null,
        'Exam' => isset($_GET['proofExam']) && $_GET['proofExam'] == 'on'
    ],
    'abs' => [
        'DateStart' => isset($_GET['absDateStart']) && $_GET['absDateStart'] != '' ? $_GET['absDateStart'] : null,
        'DateEnd' => isset($_GET['absDateEnd']) && $_GET['absDateEnd'] != '' ? $_GET['absDateEnd'] : null,
        'State' => isset($_GET['absState']) && $_GET['absState'] != '' ? $_GET['absState'] : null,
        'Exam' => isset($_GET['absExam']) && $_GET['absExam'] == 'on',
        'Locked' => isset($_GET['absLocked']) && $_GET['absLocked'] == 'on'
    ]
];
?>

<!-- Tab bar -->
<ul class="nav nav-tabs" id="tab-dashboard-stu" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link <?= $currTab == 'proof' ? 'active' : '' ?>" id="proof-tab" data-bs-toggle="tab" data-bs-target="#proof-tab-pane" type="button" role="tab" aria-controls="proof-tab-pane" aria-selected="true">Justificatifs à traité</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link <?= $currTab == 'abs' ? 'active' : '' ?>" id="absence-tab" data-bs-toggle="tab" data-bs-target="#absence-tab-pane" type="button" role="tab" aria-controls="absence-tab-pane" aria-selected="false">Justificatifs traités</button>
    </li>
</ul>
<div class="tab-content bg-white border-bottom border-start border-end rounded-bottom pt-4" id="tab-dashboard-stuContent">

    <!-- Contenu de l'onglet "Justificatifs" -->
    <div class="tab-pane fade show <?= $currTab == 'proof' ? 'active' : '' ?>" id="proof-tab-pane" role="tabpanel" aria-labelledby="proof-tab" tabindex="0">
        <?php
        $states = StateJustif::getAll();
        $currTabValue = 'proof';
        require "filter_bar.php";
        ?>

        <div class="accordion accordion-flush" id="justificationFlush">
            <?php
            $listJustifications = Justification::selectJustification(
                $_SESSION['student']->getStudentId(),
                $filter['proof1']['DateStart'],
                $filter['proof1']['DateEnd'],
                $filter['proof1']['State'],
                $filter['proof1']['Exam']
            );

            foreach($listJustifications as $justification) {
                require "lineJustification.php";
            }

            if(count($listJustifications) == 0) {
                echo "<p class='fs-3 text-body-secondary text-center p-3 m-0'>Pas de justificatif à traité</p>";
            }
            ?>
        </div>

    </div>
    <!-- Contenu de l'onglet "Absence" -->
    <div class="tab-pane fade show <?= $currTab == 'abs' ? 'active' : '' ?>" id="absence-tab-pane" role="tabpanel" aria-labelledby="absence-tab" tabindex="0">
        <?php
        $states = StateJustif::getAll();
        $currTabValue = 'proof';
        require "filter_bar.php";
        ?>

        <div class="accordion accordion-flush" id="justificationFlush">
            <?php
            $listJustifications = Justification::selectJustification(
                $_SESSION['student']->getStudentId(),
                $filter['proof2']['DateStart'],
                $filter['proof2']['DateEnd'],
                $filter['proof2']['State'],
                $filter['proof2']['Exam']
            );

            foreach($listJustifications as $justification) {
                require "lineJustification.php";
            }

            if(count($listJustifications) == 0) {
                echo "<p class='fs-3 text-body-secondary text-center p-3 m-0'>Pas de justificatif traité</p>";
            }
            ?>
        </div>
    </div>
</div>