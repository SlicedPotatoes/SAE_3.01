<?php
// ton PHP éventuel ici
?>
<link rel="stylesheet" href="/style/bootstrap.min.css">
<link rel="stylesheet" href="/style/style.css">

<style>
    .page {
        display: none;
    }
    .page.active {
        display: block;
    }

</style>

<body>

<section class="page" id="page1">
    <div class="container my-5 " >
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h1 class="card-title display-6 mb-3">Manuel d'utilisation</h1>
                <p class="text-muted">Explication de votre dashboard :</p>
                <h2 class="h5 mt-4">Tableau de bord des justificatifs</h2>
                <div class="row align-items-center mt-3">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <figure class="mb-0">
                            <img src="/img/tutoEleveDashboard.png" alt="Tuto élève - Tableau de bord" class="img-fluid rounded shadow-sm w-100" style="max-width:500px; height:auto; object-fit:contain;">
                            <figcaption class="small text-muted mt-2">Capture d'écran : votre tableau de bord.</figcaption>
                        </figure>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><span style="color: #3787FE; font-weight: bold ">1. Nombre d'absences total sur le semestre.</span></li>
                            <li class="list-group-item"><span style="color: #0ECAF0; font-weight: bold ">2. Nombre d'absences à justifier pour le semestre en cour.</span></li>
                            <li class="list-group-item"><span style="color: #FFCD2D; font-weight: bold ">3. Nombre d'absences qui vous impliquent un malus sur vos moyennes.</span></li>
                            <li class="list-group-item"><span style="color: #E76E79; font-weight: bold ">4. Visualisation du malus.</span></li>
                            <li class="list-group-item"><span style="color: #FF5A08; font-weight: bold ">5. Barre de filtre pour faciliter vos recherches.</span></li>
                            <li class="list-group-item"><span style="color: #600080; font-weight: bold ">6. Visualisation de tous vos justificatifs.</span></li>
                            <li class="list-group-item"><span style="color: #6C757D; font-weight: bold ">7. État du justificatif.</span></li>
                            <li class="list-group-item"><span style="color: #008055; font-weight: bold ">8. Visualisation des détailles de votre justificatif (celui-ci ne peut pas être modifié).</span></li>
                        </ul>
                    </div>
                </div>
                <div class="mt-4 d-flex justify-content-between">
                    <a href="/" class="btn btn-outline-secondary rounded-pill px-4">Retour à l'accueil</a>
                    <button class="btn btn-primary rounded-pill px-4" type="button" onclick="changePage(2)">Page suivante</button>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="page" id="page2">
    <div class="container my-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h1 class="card-title display-6 mb-3">Manuel d'utilisation</h1>
                <h2 class="h5 mt-4">Détaille du justificatif</h2>
                <div class="row align-items-center mt-3">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <figure class="mb-0">
                            <img src="/img/tutoEleveJustifyDetail.png" alt="Tuto élève - Détaille du justificatif" class="img-fluid rounded shadow-sm w-100" style="max-width:600px; height:auto; object-fit:contain;">
                            <figcaption class="small text-muted mt-2">Capture d'écran : détaille du justificatif.</figcaption>
                        </figure>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><span style="color: #80D6FF; font-weight: bold ">1. Dates de début et de fin de l'absence.</span></li>
                            <li class="list-group-item"><span style="color: #808285; font-weight: bold ">2. État du justificatif.</span></li>
                            <li class="list-group-item"><span style="color: #E61B1B; font-weight: bold ">3. Liste des absences pour ce justificatif.</span></li>
                            <li class="list-group-item"><span style="color: #004DE6; font-weight: bold ">4. État de l'absence.</span></li>
                            <li class="list-group-item"><span style="color: #FF5500; font-weight: bold ">5. Motif que vous avez fournis.</span></li>
                            <li class="list-group-item"><span style="color: #3D00B8; font-weight: bold ">6. Fichier de justification que vous avez fournis (ou non).</span></li>
                            <li class="list-group-item"><span style="color: #FFE600; font-weight: bold ">7. Réponse du responsable pédagogique (si elle a été donnée).</span></li>
                        </ul>
                    </div>
                </div>
                <div class="mt-4 d-flex justify-content-between">
                    <button class="btn btn-secondary rounded-pill px-4" type="button" onclick="changePage(1)">Page précédente</button>
                    <button class="btn btn-primary rounded-pill px-4" type="button" onclick="changePage(3)">Page suivante</button>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="page" id="page3">
    <div class="container my-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h1 class="card-title display-6 mb-3">Manuel d'utilisation</h1>
                <h2 class="h5 mt-4">Les Absences</h2>
                <div class="row align-items-center mt-3">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <figure class="mb-0">
                            <img src="/img/tutoEleveAbs.png" alt="Tuto élève - Absences" class="img-fluid rounded shadow-sm w-100" style="max-width:600px; height:auto; object-fit:contain;">
                            <figcaption class="small text-muted mt-2">Capture d'écran : dashboard Absences.</figcaption>
                        </figure>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><span style="color: #3D00B8; font-weight: bold ">1. Bouton pour justifier les absences.</span></li>
                            <li class="list-group-item"><span style="color: #FFE600; font-weight: bold ">2. Liste des absences de l'année.</span></li>
                        </ul>
                    </div>
                </div>
                <div class="mt-4 d-flex justify-content-between">
                    <button class="btn btn-secondary rounded-pill px-4" type="button" onclick="changePage(2)">Page précédente</button>
                    <button class="btn btn-primary rounded-pill px-4" type="button" onclick="changePage(4)">Page suivante</button>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="page mb-5" id="page4">
    <div class="container my-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h1 class="card-title display-6 mb-3">Manuel d'utilisation</h1>
                <p class="text-muted">
                    Après avoir cliqué sur le bouton :
                    <button type="button" class="btn btn-primary btn-uphf d-inline-block align-middle ms-2 rounded-bottom-0"
                            data-bs-toggle="modal" data-bs-target="#justifyModal"
                            aria-selected="false" tabindex="-1" role="tab">
                        Justifier une absence
                    </button>,
                    situé sur votre tableau de bord,<br>
                    Veuillez suivre la procédure ci-dessous :
                </p>

                <h2 class="h5 mt-4">Justification des absences</h2>

                <div class="row align-items-center mt-3">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <figure class="mb-0">
                            <img src="/img/tutoEleveJustification.png" alt="Tuto élève - Justification des absences" class="img-fluid rounded shadow-sm w-100" style="max-width:600px; height:auto; object-fit:contain;">
                            <figcaption class="small text-muted mt-2">Capture d'écran : procédure de justification des absences.</figcaption>
                        </figure>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><span style="color: #E61B1B; font-weight: bold ">1. Entrez la date de début et la date de fin de votre absence.</span></li>
                            <li class="list-group-item"><span style="color: #FF5500; font-weight: bold ">2. Sélectionnez votre ou vos justificatifs d'absence (si vous en avez).</span></li>
                            <li class="list-group-item"><span style="color: #3D00B8; font-weight: bold ">3. Dans la case « Motif de l'absence », ajoutez le(s) motif(s) et des précisions si nécessaire.</span></li>
                            <li class="list-group-item"><span style="color: #008055; font-weight: bold ">4. Envoyez votre justificatif au responsable pédagogique.</span></li>
                        </ul>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-between">
                    <button class="btn btn-secondary rounded-pill px-4" type="button" onclick="changePage(3)">Page précédente</button>
                    <a href="/" class="btn btn-primary rounded-pill px-4">Retour à l'accueil</a>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Ajustements mineurs pour l'affichage du manuel */
        .card-title { color: #0d6efd; }
        .btn-primary { background-color: #0b5ed7; border-color: #0b5ed7; }
    </style>
</section>


</body>

<script>
    let currentPage = 1;
    const totalPages = 4;

    function showPage(page) {
        document.querySelectorAll('.page').forEach(section => {
            section.classList.remove('active');
        });

        const section = document.getElementById('page' + page);
        if (section) {
            section.classList.add('active');
        }

        document.getElementById('btnPrev').disabled = (page === 1);
        document.getElementById('btnNext').disabled = (page === totalPages);

        currentPage = page;
    }

    function changePage(page) {
        if (page < 1 || page > totalPages) return;
        showPage(page);
    }

    document.addEventListener('DOMContentLoaded', function () {
        showPage(1);
    });
</script>
