<?php
/**
 * Frontend du bouton parametre
 */

use Uphf\GestionAbsence\Model\Account\AccountType;

require_once __DIR__ . "/Modal/modalLogOut.html";
?>

<!-- Bouton pour avoir le dropdown menu pour modifier les informations profiles, nous pourrons ajouter d'autres fonctionnalité plus tard -->
<div class="d-flex justify-content-end px-3 mt-3 position-absolute top-0 end-0">
    <div class="dropdown">
        <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-gear"></i>
        </button>
        <ul class="dropdown-menu">
            <li>
                <a class="dropdown-item" href="mdp.php">Modifier le mot de passe</a>
            </li>
            <li>
                <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#logoutModal">
                    Déconnexion
                </button>
            </li>
        </ul>
    </div>
</div>