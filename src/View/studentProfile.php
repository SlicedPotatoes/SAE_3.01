<?php
/**
 * Page avec onglet et contenue, affichant les justificatifs et les absences pour le profil étudiant
 */

use Uphf\GestionAbsence\Model\Entity\Justification\StateJustif;
use Uphf\GestionAbsence\Model\Entity\Absence\StateAbs;

global $dataView;

require_once __DIR__ . "/Composants/header.php";
?>

<div class="card p-3 flex-fill d-flex flex-column" style="min-height: 0">
    <!-- Tab bar -->
    <ul class="nav nav-tabs" id="tab-dashboard-stu" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="text-black nav-link <?= $dataView->currTab == 'proof' ? 'active' : '' ?>" id="proof-tab" data-bs-toggle="tab" data-bs-target="#proof-tab-pane" type="button" role="tab" aria-controls="proof-tab-pane" aria-selected="true">Justificatifs</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="text-black nav-link <?= $dataView->currTab == 'abs' ? 'active' : '' ?>" id="absence-tab" data-bs-toggle="tab" data-bs-target="#absence-tab-pane" type="button" role="tab" aria-controls="absence-tab-pane" aria-selected="false">Absences</button>
        </li>
        <li class="ms-auto">
            <?php
            require __DIR__ . "/Composants/Modal/modalJustificationAbsence.php";
            ?>
        </li>
    </ul>

    <div class="tab-content bg-white border-bottom border-start border-end rounded-bottom pt-3 flex-fill d-flex flex-column" style="min-height: 0" id="tab-dashboard-stuContent">
        <!-- Contenu de l'onglet "Justificatifs" -->
        <div class="tab-pane fade show <?= $dataView->currTab == 'proof' ? 'active d-flex' : '' ?> flex-column flex-fill" style="min-height: 0" id="proof-tab-pane" role="tabpanel" aria-labelledby="proof-tab" tabindex="0">
            <?php
            $states = StateJustif::getAll();
            $tabName = 'proof';
            $filter = $dataView->currTab === $tabName ? $dataView->filterVM->filter : [];
            require __DIR__ . "/Composants/filter_bar.php";
            ?>

            <div class="h-100 overflow-y-auto" id="justificationFlush">
                <?php
                $listJustifications = $dataView->justifications;

                foreach($listJustifications as $justification) {
                    require __DIR__ . "/Composants/lineJustification.php";
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
        <div class="tab-pane fade show <?= $dataView->currTab == 'abs' ? 'active d-flex' : '' ?> flex-column flex-fill" style="min-height: 0"  id="absence-tab-pane" role="tabpanel" aria-labelledby="absence-tab" tabindex="0">
            <?php
            $states = StateAbs::getAll();
            $tabName = 'abs';
            $filter = $dataView->currTab == $tabName ? $dataView->filterVM->filter : [];
            require __DIR__ . "/Composants/filter_bar.php";
            ?>

            <div class="accordion accordion-flush h-100 overflow-y-auto" id="absFlush">
                <?php
                $index = 0;
                $listAbs = $dataView->absences;

                foreach($listAbs as $abs) {
                    require __DIR__ . "/Composants/lineAbs.php";
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
    <script src="/script/tabBarHandler.js"></script>
</div>

<script>
    document.addEventListener('DOMContentLoaded', ()=>{ const m = document.getElementById('modaleRule'); if (m) new bootstrap.Modal(m).show(); });
</script>
