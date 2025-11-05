<?php
/**
 * Front d'une ligne d'un justificatif dans le dashboard.
 */

use Uphf\GestionAbsence\Model\Account\AccountType;

global $justification;
?>
<div class="d-flex align-items-center gap-3 p-3 border-bottom">
    <div class="d-flex flex-column">
        <div>Date de début: <?= $justification->getStartDate()->format('d/m/Y') ?></div>
        <div>Date de fin: <?= $justification->getEndDate()->format('d/m/Y') ?></div>
    </div>

    <?php if($_SESSION["role"] === AccountType::EducationalManager) : ?>
    <div class="d-flex flex-column">
        <div><?= $justification->getStudent()->getFirstName() ?> <?= $justification->getStudent()->getLastName() ?></div>
    </div>
    <?php endif; ?>

    <div class="d-flex align-items-center gap-3 flex-grow-1">
        <span class='badge rounded-pill text-bg-<?= $justification->getCurrentState()->colorBadge() ?>'><?= $justification->getCurrentState()->label() ?></span>
    </div>

    <a href="?currPage=detailsJustification&id=<?= $justification->getIdJustification(); ?>" class="text-decoration-none">
        <button class="btn btn-uphf" type="button">
            Voir les détails
        </button>
    </a>
</div>
