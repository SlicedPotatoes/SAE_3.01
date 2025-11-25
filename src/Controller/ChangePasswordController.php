<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\DB\Select\AccountSelector;
use Uphf\GestionAbsence\Model\DB\Update\PasswordUpdate;
use Uphf\GestionAbsence\Model\Notification\Notification;
use Uphf\GestionAbsence\Model\Notification\NotificationType;
use Uphf\GestionAbsence\Model\Validation\ChangePasswordValidator;
use Uphf\GestionAbsence\ViewModel\BaseViewModel;

class ChangePasswordController{

    public static function show($params): ControllerData {
        /**
         * - Si l'utilisateur est connecté -> ChangePasswordLogged()
         * - Si l'utilisateur n'est pas connecté et qu'il n'y a pas de token -> Redirection /
         * - Si l'utilisateur n'est pas connecté et qu'il y a un token -> changePasswordToken()
         */
        if(!AuthManager::isLogin()) {
            if(!isset($params['token'])) {
                header("Location: /");
                exit();
            }
            else {
                self::changePasswordToken($params['token']);

                return new ControllerData(
                    "/View/changePassword.php",
                    "Changer le mot de passe",
                    new BaseViewModel()
                );
            }
        }
        else {
            self::changePasswordLogged();

            return new ControllerData(
                "/View/changePassword.php",
                "Changer le mot de passe",
                new BaseViewModel()
            );
        }
    }

    private static function changePasswordToken($token): void {
        if($_SERVER['REQUEST_METHOD'] != "POST") {
            return;
        }
    }

    private static function changePasswordLogged(): void {
        if($_SERVER['REQUEST_METHOD'] != "POST") {
            return;
        }

        $validator = new ChangePasswordValidator();
        $errors = $validator->checkAllGood();

        if(count($errors) != 0) {
            foreach($errors as $error) {
                Notification::addNotification(NotificationType::Error, $error);
            }
            return;
        }

        $datas = $validator->getData();

        $idAccount = AuthManager::getAccount()->getIdAccount();
        $currHash = AccountSelector::getPasswordHashedById($idAccount);

        if(!password_verify($datas['lastPassword'], $currHash)) {
            Notification::addNotification(NotificationType::Error, "Changement de mot de passe: L'ancien mot de passe ne correspond pas");
            return;
        }

        self::changePassword($idAccount, $datas['newPassword']);
    }

    private static function changePassword($idAccount, $newPassword): void {
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        PasswordUpdate::updatePassword($idAccount, $passwordHash);

        Notification::addNotification(NotificationType::Success, "Votre mot de passe a bien été changé !");
    }
}

