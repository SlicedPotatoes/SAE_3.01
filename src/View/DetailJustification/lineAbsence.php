<?php
/**
 * Affichage d'une absence dans detail-justification, version pc
 */
use Uphf\GestionAbsence\Model\Entity\Absence\StateAbs;
?>

<div class="row-content">
    <div><?= $absence->getTime()->format("d/m/Y") ?></div>
    <div><?= $absence->getTime()->format("H:i") ?></div>
    <?php [$hours, $minutes] = explode(":", $absence->getDuration()); ?>
    <div><?= intval($hours) . "h" . $minutes; ?></div>
    <div><?= $absence->getResource()->getLabel() ?></div>
    <div><?= $absence->getTeacher() !== null ? $absence->getTeacher()->getFirstName() . " " . $absence->getTeacher()->getLastName() : 'Autonomie' ?></div>
    <div>
        <?php if($absence->getExamen()): ?>
            <span class='badge rounded-pill text-bg-warning'>Examen</span>
        <?php endif; ?>
    </div>
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
</div>