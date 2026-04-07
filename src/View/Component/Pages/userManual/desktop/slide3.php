<div class="slide scroll-parent gap-3" data-slide-group="manual-desktop" style="display: none !important;">
    <h4>Détail justificatif (2/2)</h4>
    <img class="img-fluid rounded shadow-sm manual-illustration manual-clickable" src="/img/desktop/tutoEleveDetailsJustifDesktop-2.png" alt="Détail justificatif desktop 2">
    <ul class="list-group list-group-flush">
        <li class="list-group-item"><span style="color: #E61B1B; font-weight: bold;">1. Liste des absences pour ce justificatif.</span></li>
        <li class="list-group-item"><span style="color: #004DE6; font-weight: bold;">2. Etat de l'absence.</span></li>
    </ul>

    <?php
    $currSlide = 2;
    $nbSlides = 5;

    $prevLabel = 'Précédent';
    $prevIcon = 'bi-caret-left-square-fill';
    $prevAction = 'showSlide';
    $prevParam = 'manual-desktop:1';

    $nextLabel = 'Suivant';
    $nextIcon = 'bi-caret-right-square';
    $nextAction = 'showSlide';
    $nextParam = 'manual-desktop:3';

    require __DIR__ . '/../../detailJustification/actionsBar.php';
    ?>
</div>
