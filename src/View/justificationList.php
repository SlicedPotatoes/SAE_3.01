<?php
/**
 * Page avec onglet et contenue, affichant les justificatifs "traité" et "non traité" le Responsable pédagogique
 */

use Uphf\GestionAbsence\Model\Entity\Justification\StateJustif;

global $dataView;

require_once __DIR__ . "/Composants/header.php";

$states = StateJustif::getAll();
?>

<div class="card p-3 flex-fill d-flex flex-column" style="min-height: 0">
    <!-- Tab bar -->
    <ul class="nav nav-tabs" id="tab-dashboard-stu" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="text-black nav-link <?= $dataView->currTab == 'proofToDo' ? 'active' : '' ?>" id="proofToDo-tab" data-bs-toggle="tab" data-bs-target="#proofToDo-tab-pane" type="button" role="tab" aria-controls="proofToDo-tab-pane" aria-selected="true">Justificatifs à traiter</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="text-black nav-link <?= $dataView->currTab == 'proofDone' ? 'active' : '' ?>" id="proofDone-tab" data-bs-toggle="tab" data-bs-target="#proofDone-tab-pane" type="button" role="tab" aria-controls="proofDone-tab-pane" aria-selected="true">Justificatifs traités</button>
        </li>
    </ul>

    <div class="tab-content bg-white border-bottom border-start border-end rounded-bottom pt-3 flex-fill d-flex flex-column" style="min-height: 0" id="tab-dashboard-stuContent">
        <!-- Contenu de l'onglet "Justificatifs" -->
        <div class="tab-pane fade show <?= $dataView->currTab == 'proofToDo' ? 'active d-flex' : '' ?> flex-column flex-fill" style="min-height: 0" id="proofToDo-tab-pane" role="tabpanel" aria-labelledby="proofToDo-tab" tabindex="0">
            <?php
            $tabName = "proofToDo";
            $filter = $dataView->currTab === $tabName ? $dataView->filterVM->filter : [];
            require __DIR__ . "/Composants/filter_bar.php";
            ?>

            <div class="h-100 overflow-y-auto" id="justificationFlush">
                <?php
                $listJustifications = $dataView->justificationsToDo;

                foreach($listJustifications as $justification) {
                    require __DIR__ . "/Composants/lineJustification.php";
                }
                ?>
                <?php if (count($listJustifications) == 0): ?>
                    <div class="d-flex flex-column align-items-center justify-content-center h-100">
                        <p class='fs-1 text-body-secondary p-3'>Pas de justificatifs à traiter</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Contenu de l'onglet "Justification Done" -->
        <div class="tab-pane fade show <?= $dataView->currTab == 'proofDone' ? 'active d-flex' : '' ?> flex-column flex-fill" style="min-height: 0" id="proofDone-tab-pane" role="tabpanel" aria-labelledby="proofDone-tab" tabindex="0">
            <?php
            $tabName = 'proofDone';
            $filter = $dataView->currTab == $tabName ? $dataView->filterVM->filter : [];

            require __DIR__ . "/Composants/filter_bar.php";
            ?>

            <div class="h-100 overflow-y-auto" id="justificationFlush">
                <?php
                $listJustifications = $dataView->justificationsDone;

                foreach($listJustifications as $justification) {
                    require __DIR__ . "/Composants/lineJustification.php";
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

    <script src="/script/tabBarHandler.js"></script>
</div>
