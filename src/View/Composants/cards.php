<?php
/**
 * Résumé de l'assiduité d'un étudiant avec quelques statistiques
 */

global $dataView;

// Récupération des valeurs à afficher
$absenceTotal = $dataView->absenceTotal;
$halfdayTotal = $dataView->halfdayTotal;
$absenceAllowJustification = $dataView->absenceAllowJustification;

$malus = $dataView->malus;
$malusWithoutPending = $dataView->malusWithoutPending;
$penalizingAbsence = $dataView->penalizingAbsence;
$halfdayPenalizingAbsence = $dataView->halfdayPenalizingAbsence;
?>

<!-- Card du dashboard avec informations sur l'assiduité -->
<div class="row row-cols-2 row-cols-md-4 g-3">

    <!-- Card pour afficher les absences totaux de l'étudiant-->
    <div class="col">
        <div class="card shadow-sm border-primary text-center h-100 card-compact">
            <div class="card-body">
                <div class="card-title small mb-1">Absences totales</div>
                <div class="fs-4 text-primary mb-0">
                    <?= $absenceTotal ?>
                </div>
                <!-- Affichage des demi-journées d'absences si elles sont différentes des absences-->
                <?php if ($absenceTotal > 0): ?>
                    <div class="text-muted small">
                        Demi-journées d’absence totales :
                        <?= $halfdayTotal ?>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <!-- Card pour afficher les absences qui peuvent être justifiée-->
    <div class="col">
        <div class="card shadow-sm border-info text-center h-100 card-compact">
            <div class="card-body">
                <div class="card-title small mb-1">Absences à justifier</div>
                <div class="fs-4 text-info mb-0">
                    <?= $absenceAllowJustification ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Absences pénalisantes, contribue au malus-->
    <div class="col">
        <div class="card shadow-sm  border-warning text-center h-100 card-compact">
            <div class="card-body">
                <div style="position: absolute; right: 5px; top: 0;">
                    <i class="bi bi-question-circle-fill text-uphf opacity-75" data-bs-toggle="tooltip" data-bs-title="Comprends les absences refusés et les absences non-justifiées"></i>
                </div>
                <div class="card-title small mb-1">Absences pénalisantes</div>
                <div class="fs-4 text-warning mb-0">
                    <?= $penalizingAbsence ?>
                </div>
                <?php if ($halfdayPenalizingAbsence > 0) : ?>
                    <div class="text-muted small">
                        Demi-journées d’absence pénalisantes :
                        <?= $halfdayPenalizingAbsence ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Malus actuel et projection du malus avec justification des absences en attente -->
    <div class="col">
        <!-- Si l'étudiant à un malus la card sera rouge, sinon vert -->
        <div class="card shadow-sm border-<?= $malus > 0 ? 'danger' : 'success' ?> text-center h-100 card-compact">
            <div class="card-body">
                <div style="position: absolute; right: 5px; top: 0;">
                    <i class="bi bi-question-circle-fill text-uphf opacity-75" data-bs-toggle="tooltip" data-bs-title="Le malus est égale au nombre de demi-journées d\'absences pénalisantes"></i>
                </div>
                <div class="card-title small mb-1">Malus</div>
                <?php if($malus > 0): ?>
                    <div class="fs-4 text-danger mb-0">-<?= $malus ?>&nbsp;</div>
                <?php else: ?>
                    <div class="fs-4 text-success mb-0">Pas de malus</div>
                <?php endif; ?>
                <!--  Si l'étudiant a des absences en attente alors le malus pourrait être réduit ou même retiré
                      Ainsi, il est important de montrer à l'étudiant l'impacte de la justification de ses absences
                      sur le malus.-->
                <?php if ($malusWithoutPending !== $malus): ?>
                    <div class="text-muted small">
                        Malus si validation des absences : -
                        <?= $malusWithoutPending ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>