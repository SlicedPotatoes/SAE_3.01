<?php

namespace Uphf\GestionAbsence\Controller;

use Respect\Validation\Exceptions\NestedValidationException;
use Uphf\GestionAbsence\Exception\BadCredentialException;
use Uphf\GestionAbsence\Exception\EntityNotFoundException;
use Uphf\GestionAbsence\Exception\InvalidPasswordConfirmationException;
use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\Notification\Notification;
use Uphf\GestionAbsence\Model\Notification\NotificationType;
use Uphf\GestionAbsence\Service\AccountService;
use Uphf\GestionAbsence\Utils\Renderer;
use Uphf\GestionAbsence\Validator\ChangePasswordValidator;

/**
 * Controller de vue relatif aux différentes Views pour le changement de mot de passe / mot de passe oublié
 *
 *  - GET /changement-de-mot-de-passe -> showConnectedChangePassword()
 *  - POST /changement-de-mot-de-passe -> postConnectedChangePassword()
 *  - GET /mot-de-passe-oublie -> showLostPassword()
 *  - POST /mot-de-passe-oublie -> postLostPassword()
 *  - GET /mot-de-passe-oublie/{token} -> showTokenChangePassword()
 *  - POST /mot-de-passe-oublie/{token} -> postTokenChangePassword()
 */

class ChangePasswordController {
    /**
     * Affiche la vue pour changer le mot de passe d'un compte dans le cas ou l'utilisateur est connecté
     *
     * @return void
     */
    public static function showConnectedChangePassword(): void {
        Renderer::render(
            'changePassword.php',
            'Changer le mot de passe',
            ['haveToken' => false]
        );
    }

    /**
     * Traitement d'un changement de mot de passe, dans le cas où l'utilisateur fourni son mot ancien mot de passe comme preuve d'authentification
     *
     * @return void
     */
    public static function postConnectedChangePassword(): void {
        try {
            ChangePasswordValidator::validateConnectedChangePassword($_POST);

            AccountService::changePasswordWithOldPassword(AuthManager::getAccount(), $_POST['lastPassword'], $_POST['newPassword']);
            Notification::addNotification(NotificationType::Success, "Votre mot de passe a bien été changé !");
        }
        catch (BadCredentialException $e) {
            Notification::addNotification(NotificationType::Error, "Changement de mot de passe: L'ancien mot de passe ne correspond pas");
        }
        catch (NestedValidationException $e) {
            foreach ($e->getMessages() as $message) {
                Notification::addNotification(NotificationType::Error, $message);
            }
        }
        catch (InvalidPasswordConfirmationException $e) {
            Notification::addNotification(NotificationType::Error, $e->getMessage());
        }
        // Ne devrait pas se produire.
        catch (EntityNotFoundException $e) {
            Notification::addNotification(NotificationType::Error, "Erreur interne");
        }

        Renderer::render(
            'changePassword.php',
            'Changer le mot de passe',
            ['haveToken' => false]
        );
    }

    /**
     * Affiche la vue mot de passe oublié
     *
     * @return void
     */
    public static function showLostPassword(): void {
        Renderer::render(
            'PasswordLost.php',
            'Mot de passe oublié',
        );
    }

    /**
     * Traite la demande de mot de passe oublié
     *
     * @return void
     */
    public static function postLostPassword(): void {
        $notificationType = NotificationType::Success;
        $message = "Si un compte correspondant à cette adresse existe, un email de réinitialisation vient de vous être envoyés.";

        try {
            ChangePasswordValidator::validatePasswordLost($_POST);
            AccountService::passwordLost($_POST['email']);
        }
        catch (EntityNotFoundException $e) {}
        catch (NestedValidationException $e) {
            $notificationType = NotificationType::Error;
            $message = $e->getMessages()[0];
        }

        Notification::addNotification($notificationType, $message);

        Renderer::render(
            'PasswordLost.php',
            'Mot de passe oublié',
        );
    }

    /**
     * Affiche la vue pour changer de mot de passe à partir d'un token
     *
     * Dans le cas ou le token fournis dans l'URL est invalide / expiré, renvoie vers 403.
     *
     * @param $params
     * @return void
     */
    public static function showTokenChangePassword($params): void {
        if(!AccountService::isValidToken($params['token'])) {
            Notification::addNotification(NotificationType::Error, "Token expiré");
            Renderer::render403();
            return;
        }

        Renderer::render(
            'changePassword.php',
            'Changer le mot de passe',
            ['haveToken' => true]
        );
    }

    /**
     * Traite le changement de mot de passe à partir d'un token
     *
     * Dans le cas ou le token fournis dans l'URL est invalide / expiré, renvoie vers 403
     *
     * Dans le cas ou les données fournis dans le formulaire par l'utilisateur son invalide, renvoie vers le formulaire.
     *
     * Dans le cas d'un succés, renvoie vers la page login.
     *
     * @param $params
     * @return void
     */
    public static function postTokenChangePassword($params): void {
        try {
            ChangePasswordValidator::validateTokenChangePassword($_POST);
            AccountService::changePasswordWithToken($params['token'], $_POST['newPassword']);
            Notification::addNotification(NotificationType::Success, "Votre mot de passe a bien été changé !");

            Renderer::render('../ViewOLD/login.php', 'Connexion');
            return;
        }
        catch (NestedValidationException $e) {
            foreach ($e->getMessages() as $message) {
                Notification::addNotification(NotificationType::Error, $message);
            }
        }
        catch (InvalidPasswordConfirmationException $e) {
            Notification::addNotification(NotificationType::Error, $e->getMessage());
        }
        catch (EntityNotFoundException $e) {
            Notification::addNotification(NotificationType::Error, "Token expiré");
            Renderer::render403();
            return;
        }

        Renderer::render(
            'changePassword.php',
            'Changer le mot de passe',
            ['haveToken' => true]
        );
    }
}