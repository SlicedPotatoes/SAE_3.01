<?php
/**
 * Barre d'action ce trouvant en bas de chaque slide de detail-justification
 *
 * Configuré avec les variables:
 * - $prevLabel => Texte du bouton à gauche
 * - $prevIcon => Icone du bouton à gauche
 * - $prevAction => Action du bouton à gauche (à link avec un script JS)
 *
 * - $nextLabel, $nextIcon, $nextAction => comme les variables préfixé de prev, mais pour le bouton droit
 *
 * - $nbSlices => Nombre de slides total
 * - $currSlide => La slide courante affiché
 */
?>
<div class="d-grid align-items-center mt-auto" style="grid-template-columns: 1fr 1fr 1fr">
    <button class="btn btn-lg btn-outline-uphf w-fit-content prev" data-action="<?= $prevAction ?>" data-param="<?= $prevParam ?>">
        <i class="bi <?= $prevIcon ?>"></i>
        <div class="d-none d-lg-inline"><?= $prevLabel ?></div>
    </button>
    <div class="pagination-dots">
        <?php for($i = 0; $i < $nbSlides; $i++): ?>
            <div class="dot <?= $i === $currSlide ? 'active' : '' ?>"></div>
        <?php endfor; ?>
    </div>
    <button class="btn btn-lg btn-uphf w-fit-content ms-auto next" data-action="<?= $nextAction ?>" data-param="<?= $nextParam ?>">
        <i class="bi <?= $nextIcon ?>"></i>
        <div class="d-none d-lg-inline"><?= $nextLabel ?></div>
    </button>
</div>