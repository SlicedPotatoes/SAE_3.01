<?php
/**
 * Affichage d'une absence dans detail-justification, version mobile
 */
use Uphf\GestionAbsence\Model\Entity\Absence\StateAbs;

?>
<div class="card d-flex gap-2 p-3">
    <!-- Date heure, durée -->
    <div class="row">
        <div class="col-12 col-sm-6">
            Le <strong><?= $absence->getTime()->format("d/m/Y") ?></strong> à <strong><?= $absence->getTime()->format("H:i") ?></strong>
        </div>
        <?php [$hours, $minutes] = explode(":", $absence->getDuration()); ?>
        <div class="col-12 col-sm-6">
            <strong>Durée : </strong><?= intval($hours) . "h" . $minutes; ?>
        </div>
    </div>

    <!-- État + tag examen -->
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <?php if($isProcessed): ?>
            <div class="d-flex align-items-center gap-2">
                <span class="badge rounded-pill text-bg-<?= $absence->getCurrentState()->colorBadge() ?>"><?= $absence->getCurrentState()->label() ?></span>
                <?php if(!$absence->getAllowedJustification() && $absence->getCurrentState() === StateAbs::Refused): ?>
                    <i style="font-size: 30px" class="bi bi-file-lock2" data-bs-toggle="tooltip" data-bs-title="Le responsable pédagogique n'autorise pas la justification de cette absence"></i>
                <?php endif; ?>
            </div>
        <?php elseif ($isEducationManager): ?>
            <div class="gap-1">
                <label class="toggle">
                    <input type="checkbox" checked data-key="state" data-absence="<?= $absence->getIdAccount() ?>_<?= $absence->getTime()->format('Y-m-d H:i:s') ?>">
                    <span class="track">
                        <span class="lbl on">Valider</span>
                        <span class="lbl off">Refuser</span>
                        <span class="thumb"></span>
                    </span>
                </label>
                <button type="button" class="btn btn-danger absence-btn-lock" data-key="lock" data-absence="<?= $absence->getIdAccount() ?>_<?= $absence->getTime()->format('Y-m-d H:i:s') ?>"><i class="bi bi-lock"></i></button>
            </div>
        <?php endif; ?>

        <?php if($absence->getExamen()): ?>
            <span class='badge rounded-pill text-bg-warning'>Examen</span>
        <?php endif; ?>
    </div>
    <hr class="mt-0 mb-0">
    <!-- Professeur + ressource -->
    <div class="row">
        <div class="col-12 col-sm-6">
            <strong>Professeur : </strong>
            <?= $absence->getTeacher() !== null ? $absence->getTeacher()->getFirstName() . " " . $absence->getTeacher()->getLastName() : 'Autonomie' ?>
        </div>
        <div class="col-12 col-sm-6">
            <strong>Ressource : </strong><?= $absence->getResource()->getLabel() ?>
        </div>
    </div>
</div>