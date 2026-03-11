<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Database\Update\MailAlertUpdater;
use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\CookieManager;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\Service\MailService;

/**
 * Controller pour le changement des notifications pour le responsable pédagogique / enseigant
 */
class ChangerMailAlertControllerApi
{
    /**
     * Fonction permettant de changer les obtions de notifications envoyé par mail pour le RP et les profs
     *
     */
    public static function update()
    {
        $account = AuthManager::getAccount();

        $mailAlertTeacher = isset($_POST['notifications']['mailAlertTeacher']);
        $mailAlertEducationalManager = isset($_POST['notifications']['mailAlertEducationalManager']);

        MailService::changeMailAlert($account,
            $mailAlertTeacher,
            $mailAlertEducationalManager);

        echo json_encode(["success" => true]);
        exit();
    }
}