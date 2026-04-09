<div class="slide scroll-parent gap-3" data-slide-group="manual-mobile">
    <h4>Dashboard mobile</h4>
    <img class="img-fluid rounded shadow-sm manual-illustration manual-clickable" src="/img/mobile/tutoEleveDashboardMobile.png" alt="Dashboard mobile">
    <ul class="list-group list-group-flush">
        <li class="list-group-item"><span style="color: #84b3f8; font-weight: bold;">1. Nombre d'absences total sur le semestre.</span></li>
        <li class="list-group-item"><span style="color: #85E4F7; font-weight: bold;">2. Nombre d'absences a justifier pour le semestre en cours.</span></li>
        <li class="list-group-item"><span style="color: #fae198; font-weight: bold;">3. Nombre d'absences qui impliquent un malus sur vos moyennes.</span></li>
        <li class="list-group-item"><span style="color: #ed99a1; font-weight: bold;">4. Visualisation du malus.</span></li>
        <li class="list-group-item"><span style="color: #ffe600; font-weight: bold;">5. Filtre pour faciliter vos recherches.</span></li>
        <li class="list-group-item"><span style="color: #600080; font-weight: bold;">6. Visualisation d'un de vos justificatifs.</span></li>
        <li class="list-group-item"><span style="color: #6c757d; font-weight: bold;">7. Etat du justificatif.</span></li>
        <li class="list-group-item"><span style="color: #008cad; font-weight: bold;">8. Visualisation des details de votre justificatif (celui-ci ne peut pas etre modifie).</span></li>
        <li class="list-group-item"><span style="color: #004de6; font-weight: bold;">9. Barre de navigation.</span></li>
    </ul>

    <?php
    $currSlide = 0;
    $nbSlides = 5;

    $prevLabel = 'Accueil';
    $prevIcon = 'bi bi-house-door-fill';
    $prevAction = 'backToHome';
    $prevParam = '';

    $nextLabel = 'Suivant';
    $nextIcon = 'bi-caret-right-square';
    $nextAction = 'showSlide';
    $nextParam = 'manual-mobile:1';

    require __DIR__ . '/../../detailJustification/actionsBar.php';
    ?>
</div>
