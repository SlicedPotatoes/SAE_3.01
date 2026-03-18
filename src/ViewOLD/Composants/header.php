<?php
/**
 * Header utiliser pour tout le site afin d'avoir une cohérence graphique
 */

use Uphf\GestionAbsence\Model\CookieManager;

$message = "<p class='h3'>$firstPartMessage <span class='text-uphf fw-bold'>$secondPartMessage</span> $thirdPartMessage";
$tag = $showCards ? 'button' : 'div';
?>

<div class="mt-3<?= $showCards ? ' accordion' : '' ?>"<?= $showCards ? ' id="accordionCard"' : '' ?>>
    <div class="accordion-item border-0 bg-transparent">
        <h2 class="accordion-header">
            <<?= $tag ?>
            class="accordion-button bg-transparent shadow-none p-0 <?= $showCards && !CookieManager::getCardOpen() ? 'collapsed' : '' ?> "
            <?php if ($showCards): ?>
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#showCard"
                aria-expanded="true"
                aria-controls="showCard"
            <?php endif; ?>
            >
            <?= $message ?>
        </<?= $tag ?>>
            <div class="header-line-brand-color"></div>
        </h2>

        <?php if ($showCards): ?>
            <div id="showCard" class="accordion-collapse collapse <?= CookieManager::getCardOpen() ? 'show' : '' ?>" data-bs-parent="#accordionCard">
                <div class="accordion-body p-0 mt-3">
                    <?php require __DIR__ . "/cards.php"; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<script src="/script/headerCard.js"></script>