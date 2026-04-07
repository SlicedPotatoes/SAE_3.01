<div class="slide scroll-parent gap-3" data-slide-group="manual-desktop" style="display: none !important;">
    <h4>Détail justificatif (1/2)</h4>
    <img class="img-fluid rounded shadow-sm manual-illustration manual-clickable" src="/img/desktop/tutoEleveDetailsJustifDesktop-1.png" alt="Détail justificatif desktop 1">
    <ul class="list-group list-group-flush">
        <li class="list-group-item"><span style="color: #80D6FF; font-weight: bold;">1. Dates de debut et de fin de l'absence.</span></li>
        <li class="list-group-item"><span style="color: #808285; font-weight: bold;">2. Etat du justificatif.</span></li>
        <li class="list-group-item"><span style="color: #FF5500; font-weight: bold;">3. Motif que vous avez fournis.</span></li>
        <li class="list-group-item"><span style="color: #3D00B8; font-weight: bold;">4. Fichier de justification que vous avez fournis (ou non).</span></li>
        <li class="list-group-item"><span style="color: #FFE600; font-weight: bold;">5. Reponse du responsable pedagogique (si elle a ete donnee).</span></li>
    </ul>

    <?php
    $currSlide = 1;
    $nbSlides = 5;

    $prevLabel = 'Précédent';
    $prevIcon = 'bi-caret-left-square-fill';
    $prevAction = 'showSlide';
    $prevParam = 'manual-desktop:0';

    $nextLabel = 'Suivant';
    $nextIcon = 'bi-caret-right-square';
    $nextAction = 'showSlide';
    $nextParam = 'manual-desktop:2';

    require __DIR__ . '/../../detailJustification/actionsBar.php';
    ?>
</div>
