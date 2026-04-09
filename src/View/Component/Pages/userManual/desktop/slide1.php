<div class="slide scroll-parent gap-3" data-slide-group="manual-desktop">
    <h4>Dashboard étudiant</h4>
    <img class="img-fluid rounded shadow-sm manual-illustration manual-clickable" src="/img/desktop/tutoEleveDashbordDesktop.png" alt="Dashboard étudiant desktop">
    <ul class="list-group list-group-flush">
        <li class="list-group-item"><span style="color: #3787FE; font-weight: bold;">1. Nombre d'absences total sur le semestre.</span></li>
        <li class="list-group-item"><span style="color: #0ECAF0; font-weight: bold;">2. Nombre d'absences à justifier pour le semestre en cour.</span></li>
        <li class="list-group-item"><span style="color: #FFCD2D; font-weight: bold;">3. Nombre d'absences qui vous impliquent un malus sur vos moyennes.</span></li>
        <li class="list-group-item"><span style="color: #E76E79; font-weight: bold;">4. Visualisation du malus.</span></li>
        <li class="list-group-item"><span style="color: #FF5A08; font-weight: bold;">5. Barre de filtre pour faciliter vos recherches.</span></li>
        <li class="list-group-item"><span style="color: #600080; font-weight: bold;">6. Visualisation de tous vos justificatifs.</span></li>
        <li class="list-group-item"><span style="color: #6C757D; font-weight: bold;">7. Etat du justificatif.</span></li>
        <li class="list-group-item"><span style="color: #008055; font-weight: bold;">8. Visualisation des details de votre justificatif (celui-ci ne peut pas etre modifie).</span></li>
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
    $nextParam = 'manual-desktop:1';

    require __DIR__ . '/../../detailJustification/actionsBar.php';
    ?>
</div>
