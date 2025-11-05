<?php

use Uphf\GestionAbsence\Model\Justification\StateJustif;
use Uphf\GestionAbsence\Presentation\JustificationPresentation;

global $currTab, $filter;
?>

<!-- Tab bar -->
<ul class="nav nav-tabs" id="tab-dashboard-stu" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="text-black nav-link <?= $currTab == 'proofToDo' ? 'active' : '' ?>" id="proofToDo-tab" data-bs-toggle="tab" data-bs-target="#proofToDo-tab-pane" type="button" role="tab" aria-controls="proofToDo-tab-pane" aria-selected="true">Justificatifs à traités</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="text-black nav-link <?= $currTab == 'proofDone' ? 'active' : '' ?>" id="proofDone-tab" data-bs-toggle="tab" data-bs-target="#proofDone-tab-pane" type="button" role="tab" aria-controls="proofDone-tab-pane" aria-selected="true">Justificatifs traités</button>
    </li>
</ul>

<div class="tab-content bg-white border-bottom border-start border-end rounded-bottom pt-3 flex-fill d-flex flex-column" style="min-height: 0" id="tab-dashboard-stuContent">
    <!-- Contenu de l'onglet "Justificatifs" -->
    <div class="tab-pane fade show <?= $currTab == 'proofToDo' ? 'active d-flex' : '' ?> flex-column flex-fill" style="min-height: 0" id="proofToDo-tab-pane" role="tabpanel" aria-labelledby="proofToDo-tab" tabindex="0">
        <?php
        $states = StateJustif::getAll();
        $currTabValue = 'proofToDo';
        require __DIR__ . "/filter_bar.php";
        ?>

        <div class="h-100 overflow-y-auto" id="justificationFlush">
            <?php
            $listJustifications = JustificationPresentation::getAllJustifications($filter['proofToDo']);

            foreach($listJustifications as $justification)
            {
                require __DIR__ . "/lineJustification.php";
            }
            ?>
            <?php if (count($listJustifications) == 0): ?>
                <div class="d-flex flex-column align-items-center justify-content-center h-100">
                    <p class='fs-1 text-body-secondary p-3'>Pas de justificatifs à traité</p>
                </div>
            <?php endif; ?>
        </div>
    </div>


    <!-- Contenu de l'onglet "Justification Done" -->
    <div class="tab-pane fade show <?= $currTab == 'proofDone' ? 'active d-flex' : '' ?> flex-column flex-fill" style="min-height: 0" id="proofDone-tab-pane" role="tabpanel" aria-labelledby="proofDone-tab" tabindex="0">
        <?php
        $states = StateJustif::getAll();
        $currTabValue = 'proofDone';
        require __DIR__ . "/filter_bar.php";
        ?>

        <div class="h-100 overflow-y-auto" id="justificationFlush">
            <?php
            $listJustifications = JustificationPresentation::getAllJustifications($filter['proofDone']);

            foreach($listJustifications as $justification) {
                require __DIR__ . "/lineJustification.php";
            }
            ?>
            <?php if (count($listJustifications) == 0): ?>
                <div class="d-flex flex-column align-items-center justify-content-center h-100">
                    <p class='fs-1 text-body-secondary p-3'>Pas de justificatifs traités</p>
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