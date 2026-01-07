<?php
use Uphf\GestionAbsence\Model\AuthManager;
use \Uphf\GestionAbsence\Model\Entity\Account\AccountType;

$isEducationalManager = AuthManager::isRole(AccountType::EducationalManager);
$isSecretary = AuthManager::isRole(AccountType::Secretary);
$isTeacher = AuthManager::isRole(AccountType::Teacher);

/**
 *Simple burger menu pour le RP et peut-être pour d'autre utilisateur plus tard
 */
?>

<!-- Simple burger menu pour le RP et peut-être pour d'autre utilisateur plus tard -->
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

          <!-- BURGER MENU DU RESPONSABLE PEDAGOGIQUE -->
          <?php if ($isEducationalManager) : ?>
            <li class="nav-item"><a class="nav-link btn btn-uphf" href="/JustificationList">Justificatifs</a></li>
            <li class="nav-item"><a class="nav-link btn btn-uphf" href="/SearchStudent">Rechercher un étudiant</a></li>
            <li class="nav-item"><a class="nav-link btn btn-uphf" href="/ImportVT">Importer depuis VT</a></li>
            <li class="nav-item"><a class="nav-link btn btn-uphf" href="/resitSession">Rattrapage</a></li>
            <li class="nav-item"><a class="nav-link btn btn-uphf" href="/statistique-general">Statistiques générales</a></li>
          <?php endif; ?>

          <!-- BURGER MENU DU SECRETARIAT -->
          <?php if ($isSecretary) : ?>
          <li class="nav-item"><a class="nav-link btn btn-uphf" href="/ImportVT">Importer depuis VT</a></li>
          <li class="nav-item"><a class="nav-link btn btn-uphf" href="/resitSession">Rattrapage</a> </li>
          <?php endif; ?>

          <li class="nav-item"><a class="nav-link btn btn-uphf" href="/listOffPeriod">Ajouter des Vacances </a></li>
          <li class="nav-item"><a class="nav-link btn btn-uphf" href="/routine">Lancer la routine (seulement pour demo)</a></li>
        </ul>
    </div>
</div>