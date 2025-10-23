<?php
/**
 * Page avec onglet et contenue, affichant les justificatifs et les absences
 */

global $currTab, $filter;
?>

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
                $listJustifications = JustificationPresentation::getJustifications($filter);

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
            $listAbs = AbsencePresentation::getAbsences($filter);

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