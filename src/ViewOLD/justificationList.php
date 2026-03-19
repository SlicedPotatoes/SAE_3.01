<?php
/**
 * Page avec onglet et contenue, affichant les justificatifs "traité" et "non traité" le Responsable pédagogique
 */

use Uphf\GestionAbsence\Model\AuthManager;

$firstPartMessage = 'Bonjour';
$secondPartMessage = AuthManager::getAccount()->getFirstName() . ' ' . AuthManager::getAccount()->getLastName();
$thirdPartMessage = '!';
$showCards = false;
require_once __DIR__ . "/Composants/header.php";
?>

<div class="card p-3 flex-fill d-flex flex-column" style="min-height: 0">
    <!-- Tab bar -->
    <ul class="nav nav-tabs" id="tab-dashboard-stu" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="text-black nav-link active" id="proofToDo-tab" data-bs-toggle="tab" data-bs-target="#proofToDo-tab-pane" type="button" role="tab" aria-controls="proofToDo-tab-pane" aria-selected="true">Justificatifs à traiter</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="text-black nav-link" id="proofDone-tab" data-bs-toggle="tab" data-bs-target="#proofDone-tab-pane" type="button" role="tab" aria-controls="proofDone-tab-pane" aria-selected="true">Justificatifs traités</button>
        </li>
    </ul>

    <div class="tab-content bg-white border-bottom border-start border-end rounded-bottom pt-3 flex-fill d-flex flex-column" style="min-height: 0" id="tab-dashboard-stuContent">
        <!-- Contenu de l'onglet "Justificatifs" -->
        <div class="tab-pane fade show active d-flex flex-column flex-fill" style="min-height: 0" id="proofToDo-tab-pane" role="tabpanel" aria-labelledby="proofToDo-tab" tabindex="0">
            <?php
            $tabName = "proofToDo";
            require __DIR__ . "/Composants/filter_bar.php";
            ?>

            <div class="h-100 overflow-y-auto" id="justificationFlush">
                <?php
                foreach($data['listToDo'] as $justification) {
                    require __DIR__ . "/Composants/lineJustification.php";
                }
                ?>
                <?php if (empty($data['listToDo'])): ?>
                    <div class="d-flex flex-column align-items-center justify-content-center h-100">
                        <p class='fs-1 text-body-secondary p-3'>Pas de justificatifs à traiter</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Contenu de l'onglet "Justification Done" -->
        <div class="tab-pane fade show flex-column flex-fill" style="min-height: 0" id="proofDone-tab-pane" role="tabpanel" aria-labelledby="proofDone-tab" tabindex="0">
            <?php
            $tabName = 'proofDone';
            require __DIR__ . "/Composants/filter_bar.php";
            ?>

            <div class="h-100 overflow-y-auto" id="justificationFlush">
                <?php
                foreach($data['listDone'] as $justification) {
                    require __DIR__ . "/Composants/lineJustification.php";
                }
                ?>
                <?php if (empty($data['listDone'])): ?>
                    <div class="d-flex flex-column align-items-center justify-content-center h-100">
                        <p class='fs-1 text-body-secondary p-3'>Pas de justificatifs traités</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="/script/tabBarHandler.js"></script>
</div>
