<?php
/**
 * Seconde slide de detail-justification
 *
 * Affiche les cours concerné par le justificatif
 *
 * Etu non traité => N'affiche pas l'état de l'absence
 * RP non traité => Permet de refuser ou valider les absences
 * Etu/RP traité => Affiche l'état du cours
 */

    $nbCol = 6 + ($isProcessed || $isEducationManager ? 1 : 0);
?>

<div class="scroll-parent gap-3 slide" style="display: none!important;">
    <div class="scroll-parent">
        <div class="d-flex gap-2 align-items-center flex-wrap mb-2">
            <h4 class="mb-0">Heure de cours concerné</h4>
            <?php if($isEducationManager && !$isProcessed): ?>
                <div class="d-flex gap-2 d-lg-none">
                    <i class="btn btn-success bi bi-check2-all" data-update-all-action="state" data-update-all-value="Validated"></i>
                    <i class="btn btn-danger bi bi-x-lg" data-update-all-action="state" data-update-all-value="Refused"></i>
                    <i class="btn btn-success bi bi-unlock" data-update-all-action="lock" data-update-all-value="false"></i>
                    <i class="btn btn-danger bi bi-lock" data-update-all-action="lock" data-update-all-value="true"></i>
                </div>
            <?php endif; ?>
        </div>
        <div class="table-like scrollable" style="grid-template-columns: repeat(<?= $nbCol ?>, auto)">
            <div class="row-content table-light">
                <strong>Date</strong>
                <strong>Heure</strong>
                <strong>Durée</strong>
                <strong>Ressource</strong>
                <strong>Enseignant</strong>
                <strong>Examen</strong>
                <?php if($isProcessed): ?>
                    <strong>État</strong>
                <?php elseif ($isEducationManager): ?>
                    <strong class="d-flex align-items-center gap-2">
                        Action
                        <i class="btn btn-success bi bi-check2-all" data-update-all-action="state" data-update-all-value="Validated"></i>
                        <i class="btn btn-danger bi bi-x-lg" data-update-all-action="state" data-update-all-value="Refused"></i>
                        <i class="btn btn-success bi bi-unlock" data-update-all-action="lock" data-update-all-value="false"></i>
                        <i class="btn btn-danger bi bi-lock" data-update-all-action="lock" data-update-all-value="true"></i>
                    </strong>
                <?php endif; ?>
            </div>
            <?php
            foreach ($data['absences'] as $absence) {
                require 'lineAbsence.php';
            }
            ?>
        </div>
        <div class="d-flex flex-column gap-3 d-lg-none">
            <?php
            foreach ($data['absences'] as $absence) {
                require 'cardAbsence.php';
            }
            ?>
        </div>
    </div>

    <!-- actions -->
    <?php
    $currSlide = 1;

    $prevLabel = 'Retour';
    $prevIcon = 'bi-caret-left-square-fill';
    $prevAction = 'showSlide';
    $prevParam = 0;

    $nextLabel = $isEducationManager && !$isProcessed ? 'Saisir un commentaire' : 'Accueil';
    $nextIcon = $nextIcon = $isEducationManager && !$isProcessed ? 'bi-caret-right-square' : 'bi bi-house-door-fill';
    $nextAction = $isEducationManager && !$isProcessed ? 'showCommentSection' : 'backToHome';
    $nextParam = $isEducationManager && !$isProcessed ? 2 : '';

    require 'actionsBar.php';
    ?>
</div>
