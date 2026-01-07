<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\CookieManager;
use Uphf\GestionAbsence\Model\DB\Update\MailAlertUpdater;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;

/**
 * Controller pour le changement des notifications pour le responsable pédagogique / enseigant
 */
class ChangerMailAlertController
{
    /**
     * Fonction permettant de changer les obtions de notifications envoyé par mail pour le RP
     * @return ControllerData|void
     */
    public static function changerMailAlertEducationalManager()
    {
        // Utilisateur non connecté, rediriger vers /
        if(!AuthManager::isLogin()) {
            header("Location: /");
            exit();
        }

        if(!AuthManager::isRole(AccountType::EducationalManager)) {
            return ControllerData::get403();
        }

        $_POST['notifications']['mailAlertTeacher'] = isset($_POST['notifications']['mailAlertTeacher']);
        $_POST['notifications']['mailAlertEducationalManager'] = isset($_POST['notifications']['mailAlertEducationalManager']);

        MailAlertUpdater::updateMailAlert(
            AccountType::EducationalManager,
            AuthManager::getAccount()->getIdaccount(),
            $_POST['notifications']['mailAlertTeacher'],
            $_POST['notifications']['mailAlertEducationalManager']);

        header("Location: " . CookieManager::getLastPath());
        exit();
    }

    /**
     * Fonction permettant de changer les obtions de notifications envoyé par mail pour l'enseigant
     *
     * @return ControllerData|void
     */
    public static function changerMailAlertTeacher()
    {
        // Utilisateur non connecté, rediriger vers /
        if(!AuthManager::isLogin()) {
            header("Location: /");
            exit();
        }

        if(!AuthManager::isRole(AccountType::Teacher)) {
            return ControllerData::get403();
        }

        $_POST['notifications']['mailAlertTeacher'] = isset($_POST['notifications']['mailAlertTeacher']);

        MailAlertUpdater::updateMailAlert(
            AccountType::Teacher,
            AuthManager::getAccount()->getIdaccount(),
            $_POST['notifications']['mailAlertTeacher']);

        header("Location: " . CookieManager::getLastPath());
        exit();
    }
}