<div class="slide scroll-parent gap-3" data-slide-group="manual-mobile" style="display: none !important;">
    <h4>Justifier une absence mobile</h4>
    <img class="img-fluid rounded shadow-sm manual-illustration manual-clickable" src="/img/mobile/tutoEleveJustifyMobile.png" alt="Justifier une absence mobile">
    <ul class="list-group list-group-flush">
        <li class="list-group-item"><span style="color: #84b3f8; font-weight: bold;">1. Entrez la date de debut et la date de fin de votre absence.</span></li>
        <li class="list-group-item"><span style="color: #85E4F7; font-weight: bold;">2. Dans la case "Motif de l'absence", ajoutez le(s) motif(s) et des precisions si necessaire.</span></li>
        <li class="list-group-item"><span style="color: #fae198; font-weight: bold;">3. Selectionnez votre ou vos justificatifs d'absence (si vous en avez).</span></li>
    </ul>

    <?php
    $currSlide = 4;
    $nbSlides = 5;

    $prevLabel = 'Précédent';
    $prevIcon = 'bi-caret-left-square-fill';
    $prevAction = 'showSlide';
    $prevParam = 'manual-mobile:3';

    $nextLabel = 'Accueil';
    $nextIcon = 'bi bi-house-door-fill';
    $nextAction = 'backToHome';
    $nextParam = '';

    require __DIR__ . '/../../detailJustification/actionsBar.php';
    ?>
</div>
