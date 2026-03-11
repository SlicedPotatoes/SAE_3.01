<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Database\Update\MailAlertUpdater;
use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\CookieManager;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\Service\MailService;
use Uphf\GestionAbsence\Utils\ResponseApi\HttpStatus;
use Uphf\GestionAbsence\Utils\ResponseApi\ResponseApi;

/**
 * Controller pour le changement des notifications pour le responsable pédagogique / enseigant
 */
class ChangerMailAlertControllerApi
{
    /**
     * PUT /api/mailAlert
     * Fonction permettant de changer les obtions de notifications envoyé par mail pour le RP et les profs
     */
    public static function putMailAlert()
    {
        $account = AuthManager::getAccount();

        $mailAlertTeacher = isset($_POST['notifications']['mailAlertTeacher']);
        $mailAlertEducationalManager = isset($_POST['notifications']['mailAlertEducationalManager']);

        MailService::changeMailAlert($account,
            $mailAlertTeacher,
            $mailAlertEducationalManager);

        new ResponseApi(HttpStatus::NO_CONTENT, array())->done();
    }
}