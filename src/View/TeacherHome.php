<?php
/** Vue minimale pour le tableau de bord professeur */
//require_once __DIR__ . "/Composants/header.php";
//require $srcFolder . "/View/Composants/burgerMenu.php";
global $dataView;

?>
<h4>Ajouter le burger menu </h4>
<div class="container  border rounded-3 mx-auto my-5 my">

    <h1 class="mb-4">Bonjour Prénom Nom</h1>

    <!-- Onglets -->
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <button class="nav-link active" id="tab-non-prevue">
                Absences
            </button>
        </li>
    </ul>
    <div class="border rounded-top">
        <h5>Filter bar à ajouter j'y arrive pas :,( </h5>
        <?php
        //TODO : Ajouter la filter bar

        //    require_once __DIR__ . '/Composants/filter_bar.php';
        ?>

    </div>

    <?php
    //TODO : faire le back pour ques les cards se remplissent automatiquement.
    ?>
    <!-- Cartes cours -->
    <div class="border">
        <div class="card-body d-flex justify-content-between align-items-center mb-3 m-3" >
            <div class="ms-3">
                <p class="mb-1"><strong>Créneau :</strong> DD/MM/YYYY, hh : mm</p>
                <p class="mb-1"><strong>Nombre d'absences:</strong> XXX</p>
            </div>
            <div class="ms-5">
                <p class="mb-1"><strong>Cour :</strong> XXX</p>
                <p class="mb-0"><strong>Groupe :</strong> XX</p>
            </div>
            <button class="btn-uphf border rounded-3 ms-auto me-5">
                Voir détaille
            </button>
        </div>
    </div>

    <div class="border">
        <div class="card-body d-flex justify-content-between align-items-center mb-3 m-3">
            <div class="ms-3">
                <p class="mb-1"><strong>Créneau :</strong> DD/MM/YYYY, hh : mm</p>
                <p class="mb-1"><strong>Nombre d'absences:</strong> XXX</p>
            </div>
            <div class="ms-5">
                <p class="mb-1"><strong>Cour :</strong> XXX</p>
                <p class="mb-0"><strong>Groupe :</strong> XX</p>
            </div>
            <button class="btn-uphf border rounded-3 ms-auto me-5">
                Voir détaille
            </button>
        </div>
    </div




    <!-- Dupliquer la carte ci‑dessus pour chaque cours -->

</div>

