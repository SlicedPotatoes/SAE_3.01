<?php
/**
 * Header utiliser pour tout le site afin d'avoir une cohérence graphique
 */

use Uphf\GestionAbsence\Model\CookieManager;

global $dataView;

$headerViewModel = $dataView->headerVM;

$message = "<p class='h3'>$headerViewModel->firstPartMessage <span class='text-uphf fw-bold'>$headerViewModel->secondPartMessage</span> $headerViewModel->thirdPartMessage";
?>

<div class="mt-3 accordion" id="accordionCard">
    <div class="accordion-item border-0 bg-transparent">
        <h2 class="accordion-header">
            <button class="accordion-button bg-transparent shadow-none p-0 <?= CookieManager::getCardOpen() ? '' : 'collapsed' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#showCard" aria-expanded="<?= CookieManager::getCardOpen() ? 'true' : 'false' ?>" aria-controls="showCard">
                <?= $message ?>
            </button>
            <div class="header-line-brand-color"></div>
        </h2>

        <?php if ($dataView->headerVM->showCards): ?>
        <div id="showCard" class="accordion-collapse collapse <?= CookieManager::getCardOpen() ? 'show' : '' ?>" data-bs-parent="#accordionCard">
            <div class="accordion-body p-0 mt-3">
                <?php require __DIR__ . "/cards.php"; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<script src="/script/headerCard.js"></script>
