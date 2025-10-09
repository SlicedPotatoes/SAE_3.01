<?php
    require_once "./Model/Absence.php";
    require_once "./Model/Justification.php";

    require_once "./View/Modal/modalJustificationAbsence.html";
    require_once "./View/Modal/modalLogOut.html";

    $currTab = isset($_GET['currTab']) ? $_GET['currTab'] : 'proof';

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

    /*
    var_dump($_GET);
    echo "<br><br>";
    var_dump($filter);
    */
?>

<!-- Étudiant : Button ouvrir modal "justifier absence" -->
<div class="d-flex justify-content-end mb-3">
    <button type="button" class="btn btn-primary btn-uphf" data-bs-toggle="modal" data-bs-target="#justifyModal">
        Justifier une absence
    </button>
</div>

<!-- Tab bar -->
<ul class="nav nav-tabs" id="tab-dashboard-stu" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link <?= $currTab == 'proof' ? 'active' : '' ?>" id="proof-tab" data-bs-toggle="tab" data-bs-target="#proof-tab-pane" type="button" role="tab" aria-controls="proof-tab-pane" aria-selected="true">Justificatifs</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link <?= $currTab == 'abs' ? 'active' : '' ?>" id="absence-tab" data-bs-toggle="tab" data-bs-target="#absence-tab-pane" type="button" role="tab" aria-controls="absence-tab-pane" aria-selected="false">Absences</button>
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
                        $filter['proof']['DateStart'],
                        $filter['proof']['DateEnd'],
                        $filter['proof']['State'],
                        $filter['proof']['Exam']
                );

                foreach($listJustifications as $justification) {
                    require "lineJustification.php";
                }

                if(count($listJustifications) == 0) {
                    echo "<p class='fs-3 text-body-secondary text-center p-3 m-0'>Pas de justificatifs</p>";
                }
            ?>
        </div>

    </div>
    <!-- Contenu de l'onglet "Absence" -->
    <div class="tab-pane fade show <?= $currTab == 'abs' ? 'active' : '' ?>" id="absence-tab-pane" role="tabpanel" aria-labelledby="absence-tab" tabindex="0">
        <?php
        $states = StateAbs::getAll();
        $currTabValue = 'abs';
        require "filter_bar.php";
        ?>

        <div class="accordion accordion-flush" id="absFlush">
            <?php
            $index = 0;
            $listAbs = Absence::getAbsencesStudentFiltered(
                    $_SESSION['student']->getStudentId(),
                    $filter['abs']['DateStart'],
                    $filter['abs']['DateEnd'],
                    $filter['abs']['Exam'],
                    $filter['abs']['Locked'],
                    $filter['abs']['State']
            );
            foreach($listAbs as $abs) {
                require "lineAbs.php";
                $index++;
            }

            if(count($listAbs) == 0) {
                echo "<p class='fs-3 text-body-secondary text-center p-3 m-0'>Pas d'absences</p>";
            }
            ?>
        </div>
    </div>
</div>
