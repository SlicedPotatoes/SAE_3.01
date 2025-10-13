<?php
require_once "./View/Modal/modalLogOut.html";
?>

<!-- Bouton pour avoir le dropdown menu pour modifier les informations profiles, nous pourrons ajouter d'autres fonctionnalité plus tard -->
<div class="d-flex justify-content-end p-3 position-relative">
    <div class="dropdown">
        <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-gear"></i>
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="mdp.php">Modifier le mot de passe</a></li>
            <li>
                <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#logoutModal">
                    Déconnexion
                </button>
            </li>
        </ul>
    </div>
</div>