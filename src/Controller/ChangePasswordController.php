<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\DB\Delete\TokenDelete;
use Uphf\GestionAbsence\Model\DB\Select\AccountSelector;
use Uphf\GestionAbsence\Model\DB\Update\PasswordUpdate;
use Uphf\GestionAbsence\Model\Notification\Notification;
use Uphf\GestionAbsence\Model\Notification\NotificationType;
use Uphf\GestionAbsence\Model\Validation\ChangePasswordValidator;
use Uphf\GestionAbsence\ViewModel\BaseViewModel;
use Uphf\GestionAbsence\ViewModel\ChangePasswordViewModel;

/**
 * Controller relatif aux différentes Views pour le changement de mot de passe / mot de passe oublié
 */
class ChangePasswordController{

    /**
     * View: changePassword - Sans token (donc login)
     *
     * Si l'utilisateur n'est pas connecté => Rediriger vers login
     *
     * Si requête POST => Traité le changement du mot de passe
     *
     * @param $params
     * @return ControllerData
     */
    public static function changeWhenLogin($params): ControllerData {
        if(!AuthManager::isLogin()) {
            header("Location: /");
            exit();
        }

        // Si l'utilisateur change son mot de passe
        if($_SERVER['REQUEST_METHOD'] == "POST") {
            $validator = new ChangePasswordValidator();
            $errors = $validator->checkAllGood();

            if(count($errors) != 0) {
                foreach($errors as $error) {
                    Notification::addNotification(NotificationType::Error, $error);
                }
            }
            // Les champs sont valides.
            else {
                $datas = $validator->getData();

                $idAccount = AuthManager::getAccount()->getIdAccount();
                $currHash = AccountSelector::getPasswordHashedById($idAccount);

                // On vérifie si l'utilisateur a bien fourni son mot de passe actuel.
                if(password_verify($datas['lastPassword'], $currHash)) {
                    self::changePassword($idAccount, $datas['newPassword']);
                }
                else {
                    Notification::addNotification(NotificationType::Error, "Changement de mot de passe: L'ancien mot de passe ne correspond pas");
                }
            }
        }

        return new ControllerData(
            "/View/changePassword.php",
            "Changer le mot de passe",
            new ChangePasswordViewModel(false)
        );
    }

    /**
     * View: changePassword - Avec token (donc pas login)
     *
     * Si l'utilisateur est login => 403
     *
     * @param $params
     * @return ControllerData
     */
    public static function changeWithToken($params): ControllerData {
        // Ne devrais pas arriver
        if(!isset($params['token'])) {
            Notification::addNotification(NotificationType::Error, "Aucun token fournis");
            return ControllerData::get403();
        }

        $token = $params['token'];
        $account = AccountSelector::getAccountFromToken($token);

        // Le n'existe pas dans la BDD où est expiré
        if(!isset($account)) {
            Notification::addNotification(NotificationType::Error, "Token expiré");
            return ControllerData::get403();
        }

        // Si l'utilisateur change son mot de passe
        if($_SERVER['REQUEST_METHOD'] == "POST") {
            $validator = new ChangePasswordValidator();
            $errors = $validator->checkAllGoodToken();

            if(count($errors) != 0) {
                foreach($errors as $error) {
                    Notification::addNotification(NotificationType::Error, $error);
                }
            }
            // Les champs sont valides.
            else {
                $datas = $validator->getData();
                self::changePassword($account->getIdAccount(), $datas['newPassword']);
                TokenDelete::deleteToken($token);
            }
        }

        return new ControllerData(
            "/View/changePassword.php",
            "Changer le mot de passe",
            new ChangePasswordViewModel(true)
        );
    }

    /**
     * TODO: A faire UwU
     *
     * @param $params
     * @return ControllerData
     */
    public static function passwordLost($params): ControllerData {
        if(AuthManager::isLogin()) {
            Notification::addNotification(NotificationType::Error, "Cette page est accéssible seulement si vous n'êtes pas connecté");
            return ControllerData::get403();
        }

        return new ControllerData(
            "",
            "",
            new BaseViewModel()
        );
    }

    /**
     * Hash le password et fait appel a la methode de changement de mot de passe dans la bdd
     *
     * @param $idAccount
     * @param $newPassword
     * @return void
     */
    private static function changePassword($idAccount, $newPassword): void {
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        PasswordUpdate::updatePassword($idAccount, $passwordHash);

        Notification::addNotification(NotificationType::Success, "Votre mot de passe a bien été changé !");
    }
}

