<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Exception\BadCredentialException;
use Uphf\GestionAbsence\Exception\EntityNotFoundException;
use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\Notification\Notification;
use Uphf\GestionAbsence\Model\Notification\NotificationType;
use Uphf\GestionAbsence\Model\Validation\ChangePasswordValidator;
use Uphf\GestionAbsence\Service\AccountService;
use Uphf\GestionAbsence\ViewModel\BaseViewModel;
use Uphf\GestionAbsence\ViewModel\ChangePasswordViewModel;

/**
 * Controller relatif aux différentes Views pour le changement de mot de passe / mot de passe oublié
 */
class ChangePasswordController {
    /**
     * Affiche la vue pour changer le mot de passe d'un compte dans le cas ou l'utilisateur est connecté
     *
     * @return ControllerData
     */
    public static function showConnectedChangePassword(): ControllerData {
        return new ControllerData(
            "/View/changePassword.php",
            "Changer le mot de passe",
            new ChangePasswordViewModel(false)
        );
    }

    /**
     * Traitement d'un changement de mot de passe, dans le cas ou l'utilisateur fournis sont mot ancien mot de passe comme preuve d'authentification
     *
     * @return ControllerData
     */
    public static function postConnectedChangePassword(): ControllerData {
        $validator = new ChangePasswordValidator();
        $errors = $validator->checkAllGood();

        if(count($errors) != 0) {
            foreach($errors as $error) {
                Notification::addNotification(NotificationType::Error, $error);
            }
        }
        else {
            try {
                $datas = $validator->getData();
                AccountService::changePasswordWithOldPassword(AuthManager::getAccount(), $datas['lastPassword'], $datas['newPassword']);
                Notification::addNotification(NotificationType::Success, "Votre mot de passe a bien été changé !");
            }
            catch (BadCredentialException $e) {
                Notification::addNotification(NotificationType::Error, "Changement de mot de passe: L'ancien mot de passe ne correspond pas");
            }
            // Ne devrait pas se produire.
            catch (EntityNotFoundException $e) {
                Notification::addNotification(NotificationType::Error, "Erreur interne");
            }
        }

        return new ControllerData(
            "/View/changePassword.php",
            "Changer le mot de passe",
            new ChangePasswordViewModel(false)
        );
    }

    /**
     * Affiche la vue mot de passe oublié
     *
     * @return ControllerData
     */
    public static function showLostPassword(): ControllerData {
        return new ControllerData(
            "/View/PasswordLost.php",
            "Mot de passe oublié",
            new BaseViewModel()
        );
    }

    /**
     * Traite la demande de mot de passe oublié
     *
     * @return ControllerData
     */
    public static function postLostPassword(): ControllerData {
        $email = $_POST['email'];

        try {
            AccountService::passwordLost($email);
        }
        catch (EntityNotFoundException $e) {}

        Notification::addNotification(
            NotificationType::Success,
            "Si un compte correspondant à cette adresse existe, un email de réinitialisation vient de vous être envoyés."
        );

        return new ControllerData(
            "/View/PasswordLost.php",
            "Mot de passe oublié",
            new BaseViewModel()
        );
    }

    /**
     * Affiche la vue pour changer de mot de passe à partir d'un token
     *
     * Dans le cas ou le token fournis dans l'URL est invalide / expiré, renvoie vers 403.
     *
     * @param $params
     * @return ControllerData
     */
    public static function showTokenChangePassword($params): ControllerData {
        if(!AccountService::isValidToken($params['token'])) {
            Notification::addNotification(NotificationType::Error, "Token expiré");
            return ControllerData::get403();
        }

        return new ControllerData(
            "/View/changePassword.php",
            "Changer le mot de passe",
            new ChangePasswordViewModel(true)
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
     * @return ControllerData
     */
    public static function postTokenChangePassword($params): ControllerData {
        try {
            $validator = new ChangePasswordValidator();
            $errors = $validator->checkAllGoodToken();

            // Entrée du formulaire invalide
            if(count($errors) != 0) {
                foreach($errors as $error) {
                    Notification::addNotification(NotificationType::Error, $error);
                }

                return new ControllerData(
                    "/View/changePassword.php",
                    "Changer le mot de passe",
                    new ChangePasswordViewModel(true)
                );
            }
            // Les champs sont valides.
            else {
                $datas = $validator->getData();
                AccountService::changePasswordWithToken($params['token'], $datas['newPassword']);
                Notification::addNotification(NotificationType::Success, "Votre mot de passe a bien été changé !");

                return new ControllerData(
                    "/View/login.php",
                    "Connexion",
                    new BaseViewModel()
                );
            }
        }
        // Token invalide
        catch (EntityNotFoundException $e) {
            Notification::addNotification(NotificationType::Error, "Token expiré");
            return ControllerData::get403();
        }
    }
}