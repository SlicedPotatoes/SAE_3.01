<div class="slide scroll-parent gap-3" data-slide-group="manual-mobile" style="display: none !important;">
    <h4>Détail justificatif mobile (1/2)</h4>
    <img class="img-fluid rounded shadow-sm manual-illustration manual-clickable" src="/img/mobile/tutoEleveJustifyDetailMobile-1.png" alt="Détail justificatif mobile 1">
    <ul class="list-group list-group-flush">
        <li class="list-group-item"><span style="color: #84b3f8; font-weight: bold;">1. Etat du justificatif.</span></li>
        <li class="list-group-item"><span style="color: #85E4F7; font-weight: bold;">2. Informations utiles.</span></li>
        <li class="list-group-item"><span style="color: #fae198; font-weight: bold;">3. Motif que vous avez donne.</span></li>
        <li class="list-group-item"><span style="color: #ed99a1; font-weight: bold;">4. Fichier de justification que vous avez fourni (ou non).</span></li>
        <li class="list-group-item"><span style="color: #ffe600; font-weight: bold;">5. Commentaire du responsable pedagogique.</span></li>
        <li class="list-group-item"><span style="color: #600080; font-weight: bold;">6. Bouton de retour a l'accueil.</span></li>
        <li class="list-group-item"><span style="color: #6c757d; font-weight: bold;">7. Page suivante.</span></li>
    </ul>

    <?php
    $currSlide = 1;
    $nbSlides = 5;

    $prevLabel = 'Précédent';
    $prevIcon = 'bi-caret-left-square-fill';
    $prevAction = 'showSlide';
    $prevParam = 'manual-mobile:0';

    $nextLabel = 'Suivant';
    $nextIcon = 'bi-caret-right-square';
    $nextAction = 'showSlide';
    $nextParam = 'manual-mobile:2';

    require __DIR__ . '/../../detailJustification/actionsBar.php';
    ?>
</div>
