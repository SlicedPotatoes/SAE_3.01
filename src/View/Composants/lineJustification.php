<?php

/**
 * Front d'une ligne d'un justificatif dans le dashboard.
 */

use Uphf\GestionAbsence\Model\Entity\Account\AccountType;

global $justification;
?>
<div class="d-flex align-items-center gap-3 p-4 pb-3 pt-3 border-bottom">
    <div class="d-flex flex-column">
        <div>Date de début: <?= $justification->startDate ?></div>
        <div>Date de fin: <?= $justification->endDate ?></div>
    </div>

    <?php if($justification->roleUser === AccountType::EducationalManager) : ?>
        <div class="d-flex flex-column">
            <div><?= $justification->studentFullName ?></div>
        </div>
    <?php endif; ?>

    <div class="d-flex align-items-center gap-3 flex-grow-1">
        <span class='badge rounded-pill text-bg-<?= $justification->state->colorBadge() ?>'><?= $justification->state->label() ?></span>
    </div>

    <a href="/DetailJustification/<?= $justification->idJustification ?>" class="text-decoration-none">
        <button class="btn btn-uphf" type="button">
            Voir les détails
        </button>
    </a>
</div>
