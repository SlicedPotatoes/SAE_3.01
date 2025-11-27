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
                Date non prévue
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="tab-prevue">
                Date prévue
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
        <div class="card-body ">
            <p class="mb-1"><strong>Cours :</strong> XXX</p>
            <p class="mb-1"><strong>Professeur :</strong> XXX</p>
            <p class="mb-0"><strong>Nombre d'absences justifiées :</strong> XX</p>
        </div>
    </div>

    <!-- Dupliquer la carte ci‑dessus pour chaque cours -->

</div>

