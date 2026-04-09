<div class="slide scroll-parent gap-3" data-slide-group="manual-desktop" style="display: none !important;">
    <h4>Absences</h4>
    <img class="img-fluid rounded shadow-sm manual-illustration manual-clickable" src="/img/desktop/tutoEleveAbsDesktop.png" alt="Absences desktop">
    <ul class="list-group list-group-flush">
        <li class="list-group-item"><span style="color: #3D00B8; font-weight: bold;">1. Bouton pour justifier les absences.</span></li>
        <li class="list-group-item"><span style="color: #FFE600; font-weight: bold;">2. Liste des absences de l'annee.</span></li>
    </ul>

    <?php
    $currSlide = 3;
    $nbSlides = 5;

    $prevLabel = 'Précédent';
    $prevIcon = 'bi-caret-left-square-fill';
    $prevAction = 'showSlide';
    $prevParam = 'manual-desktop:2';

    $nextLabel = 'Suivant';
    $nextIcon = 'bi-caret-right-square';
    $nextAction = 'showSlide';
    $nextParam = 'manual-desktop:4';

    require __DIR__ . '/../../detailJustification/actionsBar.php';
    ?>
</div>
