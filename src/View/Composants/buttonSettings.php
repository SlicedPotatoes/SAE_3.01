<?php
/**
 * Frontend du bouton parametre
 */

use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;

require_once __DIR__ . "/Modal/modalLogOut.html";
if (AuthManager::isRole(AccountType::EducationalManager) || AuthManager::isRole(AccountType::Teacher)) {
    require_once __DIR__ . "/Modal/editNotificationsModal.php";
}
?>

<!-- Bouton pour avoir le dropdown menu pour modifier les informations du profil, nous pourrons ajouter d'autres fonctionnalités plus tard -->
<div class="d-flex justify-content-end px-3 mt-3 position-absolute top-0 end-0">
    <div class="dropdown">
        <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-gear"></i>
        </button>
        <ul class="dropdown-menu">
            <li>
                <a class="dropdown-item" href="/ChangePassword">Modifier le mot de passe</a>
            </li>
            <?php if (AuthManager::isRole(AccountType::EducationalManager) || AuthManager::isRole(AccountType::Teacher)) : ?>
            <li>
                <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#EditNotificationModal">
                    Gérer les notifications
                </button>
            </li>
            <?php endif; ?>
            <?php if (AuthManager::isRole(AccountType::EducationalManager)) : ?>
            <li>
                <a class="dropdown-item" href="/PredefinedComments">Commentaires prédéfinis</a>
            </li>
            <?php endif; ?>
            <?php if (AuthManager::isRole(AccountType::EducationalManager) || AuthManager::isRole(AccountType::Secretary)) : ?>
            <li>
                <a class="dropdown-item" href="/SemesterSettings">Définir les semestres</a>
            </li>
            <?php endif; ?>
            <li>
                <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#logoutModal">
                    Déconnexion
                </button>
            </li>
        </ul>
    </div>
</div>