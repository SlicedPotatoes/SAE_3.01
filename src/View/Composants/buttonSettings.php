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


<?php if($_SESSION['role'] == AccountType::EducationalManager):?>
<div class="position-absolute top-0 start-0 m-3">
    <button class="btn btn-light p-2 pt-0 pb-0" type="button"
            data-bs-toggle="offcanvas"
            data-bs-target="#offcanvasNavbar"
            aria-controls="offcanvasNavbar">
        <i class="bi bi-list" style="font-size: 30px;"></i>
    </button>
</div>

<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasNavbarLabel">Menu</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Fermer"></button>
    </div>
    <div class="offcanvas-body">
        <ul class="navbar-nav flex-grow-1 gap-2">
            <li class="nav-item"><a class="nav-link btn btn-uphf" href="index.php?currPage=justificationList">Justificatifs</a></li>
            <li class="nav-item"><a class="nav-link btn btn-uphf" href="index.php?currPage=searchStudent">Rechercher un étudiant</a></li>
        </ul>
    </div>
</div>
<?php endif;?>