<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Exception\BadCredentialException;
use Uphf\GestionAbsence\Exception\EntityNotFoundException;
use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\Notification\Notification;
use Uphf\GestionAbsence\Model\Notification\NotificationType;
use Uphf\GestionAbsence\Service\AccountService;
use Uphf\GestionAbsence\ViewModel\BaseViewModel;

/**
 * Authentification Controller
 *
 * Gère le login et logout
 */
class AuthentificationController {

    /**
     * Renvoie l'utilisateur vers la page de connexion
     *
     * @return ControllerData
     */
    public static function login(): ControllerData {
        return new ControllerData(
            "/View/login.php",
            "Connexion",
            new BaseViewModel()
        );
    }

    /**
     * Tentative de connection de l'utilisateur
     *
     * @return ControllerData|void
     */
    public static function postLogin() {
        try {
            $account = AccountService::loginAccount($_POST["email"], $_POST["password"]);

            // Connexion au niveau de la session
            AuthManager::login(
                $account->getAccountType(),
                $account
            );

            header("Location: /");
            exit();

        } catch (EntityNotFoundException | BadCredentialException) {
            Notification::addNotification(
                NotificationType::Error,
                "Email ou mot de passe incorrect"
            );
        }

        // Utilisateur n'est pas connecté, on affiche la view d'authentification
        return new ControllerData(
            "/View/login.php",
            "Connexion",
            new BaseViewModel()
        );
    }

    /**
     * Permet de déconnecter l'utilisateur et le renvoie sur la page de connexion
     * @return void
     */
    public static function logout(): void {
        AuthManager::logout();
        header("Location: /");
        exit();
    }
}