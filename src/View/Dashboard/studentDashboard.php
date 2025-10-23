<?php
/**
 * Font end du dashboard d'un étudiant
 */
    require_once "./Model/Absence/Absence.php";
    require_once "./Model/Justification/Justification.php";

    require_once "./View/Modal/modalJustificationAbsence.php";

    // Récupérer l'onglet courrant, pour pouvoir ouvrir la page sur le bon onglet
    $currTab = isset($_GET['currTab']) ? $_GET['currTab'] : 'proof';

/*
 * Tableau contenant les filtres
 * Utiliser pour la requête des filtres
 * Et également pour afficher la valeur du filtre actuel
 */
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

<div class="card p-3">
    <?php
    if ($_SESSION["role"] == AccountType::Student)
    {
        require "./View/Dashboard/Student/justificationButton.html";
    }

    require "./View/Dashboard/tabAbsencesJustificatifs.php";

    ?>
</div>
