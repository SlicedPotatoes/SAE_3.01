<?php
/**
 * Burger menu pour la navigation du RP, secrétaire et teacher.
 */

use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;

$isEducationalManager = AuthManager::isRole(AccountType::EducationalManager);
$isSecretary = AuthManager::isRole(AccountType::Secretary);

if($isEducationalManager || $isSecretary):

?>

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
          <!-- BURGER MENU DU RESPONSABLE PÉDAGOGIQUE -->
          <?php if ($isEducationalManager) : ?>
              <li class="nav-item"><a class="nav-link btn btn-uphf" href="/justifications">Justificatifs</a></li>
              <li class="nav-item"><a class="nav-link btn btn-uphf" href="/rechercher-un-etudiant">Rechercher un étudiant</a></li>
              <li class="nav-item"><a class="nav-link btn btn-uphf" href="/statistiques-generales">Statistiques générales</a></li>
              <li class="nav-item"><a class="nav-link btn btn-uphf" href="/absences-a-mes-cours">Absences a mes cours</a></li>
          <?php endif; ?>

          <!-- COMMUN AU RESPONSABLE PÉDAGOGIQUE ET SECRETAIRE -->
          <li class="nav-item"><a class="nav-link btn btn-uphf" href="/televersement">Importer depuis VT</a></li>
          <li class="nav-item"><a class="nav-link btn btn-uphf" href="/rattrapage">Rattrapage</a></li>
          <li class="nav-item"><a class="nav-link btn btn-uphf" href="/periode-de-vacances">Ajouter des Vacances </a></li>
          <li class="nav-item"><a class="nav-link btn btn-uphf" href="/routine">Lancer la routine (seulement pour demo)</a></li>
        </ul>
    </div>
</div>
<?php endif; ?>