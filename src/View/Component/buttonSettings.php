<?php

/**
 * Front du bouton de paramètre, il est affiché seulement dans le cas où l'utilisateur est connecté
 *
 * Il affiche un dropdown contenant les options suivante :
 * - Modifier le mot de passe
 * - Dans le cas d'un compte RP ou Teacher : La possibilité de gérer les notifications de mail
 * - Dans le cas d'un compte RP : La possibilité de configuré les commentaires prédéfinis
 * - Dans le cas d'un compte RP ou secrétaire : La possibilité de configuré les semestres
 * - Deconnexion
 */

use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;

if(AuthManager::isLogin()):

    require_once __DIR__ . "/Modal/modalLogOut.html";
    if (AuthManager::isRole(AccountType::EducationalManager) || AuthManager::isRole(AccountType::Teacher)) {
        require_once __DIR__ . "/Modal/editNotificationsModal.php";
    }
    ?>

    <div class="d-flex justify-content-end px-3 mt-3 position-absolute top-0 end-0">
        <div class="dropdown">
            <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-gear"></i>
            </button>
            <ul class="dropdown-menu">
                <li>
                    <a class="dropdown-item" href="/changement-de-mot-de-passe">Modifier le mot de passe</a>
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
                        <a class="dropdown-item" href="/commentaire-predefini">Commentaires prédéfinis</a>
                    </li>
                <?php endif; ?>
                <?php /* if (AuthManager::isRole(AccountType::EducationalManager) || AuthManager::isRole(AccountType::Secretary)) : ?>
                    <li>
                        <a class="dropdown-item" href="/SemesterSettings">Définir les semestres</a>
                    </li>
                <?php endif; */ ?>
                <li>
                    <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#logoutModal">
                        Déconnexion
                    </button>
                </li>
            </ul>
        </div>
    </div>

<?php endif; ?>
