<?php
    require_once "./Model/State.php";
    require_once "./Model/Absence.php";
    require_once "./Model/Justification.php";

    require_once "./View/Modal/modalJustificationAbsence.html";
    require_once "./View/Modal/modalLogOut.html";
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
        <button class="nav-link active" id="proof-tab" data-bs-toggle="tab" data-bs-target="#proof-tab-pane" type="button" role="tab" aria-controls="proof-tab-pane" aria-selected="true">Justificatifs</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="absence-tab" data-bs-toggle="tab" data-bs-target="#absence-tab-pane" type="button" role="tab" aria-controls="absence-tab-pane" aria-selected="false">Absences</button>
    </li>
</ul>
<div class="tab-content bg-white border-bottom border-start border-end rounded-bottom py-4" id="tab-dashboard-stuContent">
    <!-- Contenu de l'onglet "Justificatifs" -->
    <div class="tab-pane fade show active" id="proof-tab-pane" role="tabpanel" aria-labelledby="proof-tab" tabindex="0">
        <?php
            $states = State::getJustificationStates();
            require "filter_bar.php";
        ?>

        <div class="accordion accordion-flush" id="justificationFlush">
            <?php
                $listJustifications = Justification::getJustifications();
                foreach($listJustifications as $justification) {
                    require "lineJustification.php";
                }
            ?>
        </div>

    </div>
    <!-- Contenu de l'onglet "Absence" -->
    <div class="tab-pane fade show" id="absence-tab-pane" role="tabpanel" aria-labelledby="absence-tab" tabindex="0">
        <?php
        $states = State::getAbsenceStates();
        require "filter_bar.php";
        ?>

        <div class="accordion accordion-flush" id="absFlush">
            <?php
            $listAbs = Absence::getAbsences();
            foreach($listAbs as $abs) {
                require "lineAbs.php";
            }
            ?>
        </div>
    </div>
</div>
