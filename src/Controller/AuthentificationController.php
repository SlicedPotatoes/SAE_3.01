<?php

namespace Uphf\GestionAbsence\Controller;

use Respect\Validation\Exceptions\NestedValidationException;
use Uphf\GestionAbsence\Exception\BadCredentialException;
use Uphf\GestionAbsence\Exception\EntityNotFoundException;
use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\Notification\Notification;
use Uphf\GestionAbsence\Model\Notification\NotificationType;
use Uphf\GestionAbsence\Service\AccountService;
use Uphf\GestionAbsence\Utils\Renderer;
use Uphf\GestionAbsence\Validator\AuthentificationValidator;

/**
 * Controller de vue pour la gestion de l'Authentification
 *
 * Gère le login et logout
 *
 * - GET /connexion -> login()
 * - POST /connexion -> postLogin()
 * - GET /deconnexion -> logout()
 */
class AuthentificationController {

    /**
     * Renvoie l'utilisateur vers la page de connexion
     *
     * @return void
     */
    public static function login(): void {
        Renderer::render('../ViewOLD/login.php', 'Connexion');
    }

    /**
     * Tentative de connection de l'utilisateur
     *
     * @return void
     */
    public static function postLogin(): void {
        try {
            AuthentificationValidator::validateLogin($_POST);

            $account = AccountService::loginAccount($_POST["email"], $_POST["password"]);

            // Connexion au niveau de la session
            AuthManager::login(
                $account->getAccountType(),
                $account
            );

            header("Location: /");
            return;
        }
        catch (EntityNotFoundException | BadCredentialException) {
            Notification::addNotification(NotificationType::Error, "Email ou mot de passe incorrect");
        }
        catch (NestedValidationException $e) {
            foreach ($e->getMessages() as $error) {
                Notification::addNotification(NotificationType::Error, $error);
            }
        }

        // Utilisateur n'est pas connecté, on affiche la view d'authentification
        Renderer::render('../ViewOLD/login.php', 'Connexion');
    }

    /**
     * Permet de déconnecter l'utilisateur et le renvoie sur la page de connexion
     * @return void
     */
    public static function logout(): void {
        AuthManager::logout();
        header("Location: /");
    }
}