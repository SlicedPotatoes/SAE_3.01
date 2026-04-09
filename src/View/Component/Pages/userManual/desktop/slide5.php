<div class="slide scroll-parent gap-3" data-slide-group="manual-desktop" style="display: none !important;">
    <h4>Justifier une absence</h4>
    <img class="img-fluid rounded shadow-sm manual-illustration manual-clickable" src="/img/desktop/tutoEleveJustifyDesktop.png" alt="Justifier une absence desktop">
    <p class="text-muted mb-0">
        Apres avoir clique sur le bouton <strong>Justifier une absence</strong> situe sur votre tableau de bord,
        veuillez suivre la procedure ci-dessous.
    </p>
    <ul class="list-group list-group-flush">
        <li class="list-group-item"><span style="color: #E61B1B; font-weight: bold;">1. Entrez la date de debut et la date de fin de votre absence.</span></li>
        <li class="list-group-item"><span style="color: #FF5500; font-weight: bold;">2. Selectionnez votre ou vos justificatifs d'absence (si vous en avez).</span></li>
        <li class="list-group-item"><span style="color: #3D00B8; font-weight: bold;">3. Dans la case "Motif de l'absence", ajoutez le(s) motif(s) et des precisions si necessaire.</span></li>
        <li class="list-group-item"><span style="color: #008055; font-weight: bold;">4. Envoyez votre justificatif au responsable pedagogique.</span></li>
    </ul>

    <?php
    $currSlide = 4;
    $nbSlides = 5;

    $prevLabel = 'Précédent';
    $prevIcon = 'bi-caret-left-square-fill';
    $prevAction = 'showSlide';
    $prevParam = 'manual-desktop:3';

    $nextLabel = 'Accueil';
    $nextIcon = 'bi bi-house-door-fill';
    $nextAction = 'backToHome';
    $nextParam = '';

    require __DIR__ . '/../../detailJustification/actionsBar.php';
    ?>
</div>
