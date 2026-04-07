<div class="slide scroll-parent gap-3" data-slide-group="manual-mobile" style="display: none !important;">
    <h4>Détail justificatif mobile (2/2)</h4>
    <img class="img-fluid rounded shadow-sm manual-illustration manual-clickable" src="/img/mobile/tutoEleveJustifyDetailMobile-2.png" alt="Détail justificatif mobile 2">
    <ul class="list-group list-group-flush">
        <li class="list-group-item"><span style="color: #84b3f8; font-weight: bold;">1. Liste des absences concernees par le justificatif.</span></li>
    </ul>

    <?php
    $currSlide = 2;
    $nbSlides = 5;

    $prevLabel = 'Précédent';
    $prevIcon = 'bi-caret-left-square-fill';
    $prevAction = 'showSlide';
    $prevParam = 'manual-mobile:1';

    $nextLabel = 'Suivant';
    $nextIcon = 'bi-caret-right-square';
    $nextAction = 'showSlide';
    $nextParam = 'manual-mobile:3';

    require __DIR__ . '/../../detailJustification/actionsBar.php';
    ?>
</div>
