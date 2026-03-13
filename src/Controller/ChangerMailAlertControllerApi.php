<?php

namespace Uphf\GestionAbsence\Controller;

use PHPUnit\Exception;
use Uphf\GestionAbsence\Database\Update\MailAlertUpdater;
use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\CookieManager;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\Model\Notification\Notification;
use Uphf\GestionAbsence\Model\Notification\NotificationType;
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

        $data = json_decode(file_get_contents("php://input"), true);

        $mailAlertTeacher = $data['notifications']['mailAlertTeacher'] ?? null;
        $mailAlertEducationalManager = $data['notifications']['mailAlertEducationalManager'] ?? null;

        if (AuthManager::isRole(AccountType::Teacher)) {
            if ($mailAlertTeacher === null) {
                Notification::addNotification(
                    NotificationType::Error,
                    "Les champs obligatoires n'ont pas était remplis");
                new ResponseApi(
                    HttpStatus::BAD_REQUEST,
                    (array)"Les champs obligatoires n'ont pas était remplis"
                )->done();
                return;
            }
            $mailAlertEducationalManager = false;
        }

        if (AuthManager::isRole(AccountType::EducationalManager)) {
            if ($mailAlertTeacher === null || $mailAlertEducationalManager === null) {
                Notification::addNotification(
                    NotificationType::Error,
                    "Les champs obligatoires n'ont pas était remplis");
                new ResponseApi(
                    HttpStatus::BAD_REQUEST,
                    (array)"Les champs obligatoires n'ont pas était remplis"
                )->done();
                return;
            }
        }

        try {
            MailService::changeMailAlert(
                $account,
                $mailAlertTeacher,
                $mailAlertEducationalManager);
            new ResponseApi(
                HttpStatus::NO_CONTENT
            )->done();
        } catch (Exception $e) {
            Notification::addNotification(
                NotificationType::Error,
                "Erreur lors de la mise à jours des paramètres de notification, réessayez plus tard");
            new ResponseApi(
                HttpStatus::BAD_REQUEST,
                (array)$e->getMessage()
            );
        }
    }
}