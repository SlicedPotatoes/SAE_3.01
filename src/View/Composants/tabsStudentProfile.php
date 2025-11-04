<?php
/**
 * Page avec onglet et contenue, affichant les justificatifs et les absences pour le profil étudiant
 */

use Uphf\GestionAbsence\Model\Justification\StateJustif;
use Uphf\GestionAbsence\Presentation\JustificationPresentation;
use Uphf\GestionAbsence\Model\Absence\StateAbs;
use Uphf\GestionAbsence\Presentation\AbsencePresentation;

global $currTab, $filter;
?>

<!-- Tab bar -->
<ul class="nav nav-tabs" id="tab-dashboard-stu" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="text-black nav-link <?= $currTab == 'proof' ? 'active' : '' ?>" id="proof-tab" data-bs-toggle="tab" data-bs-target="#proof-tab-pane" type="button" role="tab" aria-controls="proof-tab-pane" aria-selected="true">Justificatifs</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="text-black nav-link <?= $currTab == 'abs' ? 'active' : '' ?>" id="absence-tab" data-bs-toggle="tab" data-bs-target="#absence-tab-pane" type="button" role="tab" aria-controls="absence-tab-pane" aria-selected="false">Absences</button>
    </li>
    <?php if (true): ?>
    <li class="ms-auto">
        <?php
        require __DIR__ . "/../Composants/Modal/modalJustificationAbsence.php";
        require __DIR__ . "/../Composants/justificationButton.html";
        ?>
    </li>
    <?php endif; ?>
</ul>

<div class="tab-content bg-white border-bottom border-start border-end rounded-bottom pt-3 flex-fill d-flex flex-column" style="min-height: 0" id="tab-dashboard-stuContent">
    <!-- Contenu de l'onglet "Justificatifs" -->
    <div class="tab-pane fade show <?= $currTab == 'proof' ? 'active' : '' ?> flex-column flex-fill d-flex" style="min-height: 0" id="proof-tab-pane" role="tabpanel" aria-labelledby="proof-tab" tabindex="0">
        <?php
            $states = StateJustif::getAll();
            $currTabValue = 'proof';
            require __DIR__ . "/filter_bar.php";
        ?>

        <div class="h-100 overflow-y-auto" id="justificationFlush">
            <?php
                $listJustifications = JustificationPresentation::getJustifications($filter['proof']);

                foreach($listJustifications as $justification) {
                    require __DIR__ . "/lineJustification.php";
                }
            ?>
            <?php if (count($listJustifications) == 0): ?>
                <div class="d-flex flex-column align-items-center justify-content-center h-100">
                    <p class='fs-1 text-body-secondary p-3'>Pas de justificatifs</p>
                </div>
            <?php endif; ?>
        </div>
    </div>


    <!-- Contenu de l'onglet "Absence" -->
    <div class="tab-pane fade show <?= $currTab == 'abs' ? 'active' : '' ?> flex-column flex-fill" style="min-height: 0"  id="absence-tab-pane" role="tabpanel" aria-labelledby="absence-tab" tabindex="0">
        <?php
        $states = StateAbs::getAll();
        $currTabValue = 'abs';
        require "filter_bar.php";
        ?>

        <div class="accordion accordion-flush h-100 overflow-y-auto" id="absFlush">
            <?php
            $index = 0;
            $listAbs = AbsencePresentation::getAbsences($filter['abs']);

            foreach($listAbs as $abs) {
                require "lineAbs.php";
                $index++;
            }
            ?>

            <?php if (count($listAbs) == 0): ?>
                <div class="d-flex flex-column align-items-center justify-content-center h-100">
                    <p class='fs-1 text-body-secondary p-3'>Pas d'absences</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    // Script pour ajouter une classe personnalisé à la div du contenu d'une tab et enlevé cette même classe à la tab précédente
    const tabsButtonDashboard = document.querySelectorAll('#tab-dashboard-stu button');

    tabsButtonDashboard.forEach((tab) => {
       // shown.bs.tab est l'event déclenché lorsqu'une tab deviens active
       tab.addEventListener('shown.bs.tab', (e) => {
          const activatedPane = document.querySelector(tab.getAttribute('data-bs-target'));
          activatedPane.classList.add('d-flex');

          if(e.relatedTarget) {
              const previousPane = document.querySelector(e.relatedTarget.getAttribute('data-bs-target'));
              previousPane.classList.remove('d-flex');
          }
       });
    });
</script>