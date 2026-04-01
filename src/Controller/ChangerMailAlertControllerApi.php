<?php

namespace Uphf\GestionAbsence\Controller;

use Respect\Validation\Exceptions\NestedValidationException;
use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\Notification\Notification;
use Uphf\GestionAbsence\Model\Notification\NotificationType;
use Uphf\GestionAbsence\Service\MailService;
use Uphf\GestionAbsence\Utils\ResponseApi\HttpStatus;
use Uphf\GestionAbsence\Utils\ResponseApi\ResponseApi;
use Uphf\GestionAbsence\Validator\ChangerMailAlertValidator;

/**
 * Controller pour le changement des notifications pour le responsable pédagogique / enseignant
 *
 * - PUT /api/mailAlert -> putMailAlert()
 */
class ChangerMailAlertControllerApi
{
    /**
     * Fonction permettant de changer les options de notifications envoyée par mail pour le RP et les profs
     */
    public static function putMailAlert(): void
    {
        $data = json_decode(file_get_contents("php://input"), true);

        try {
            ChangerMailAlertValidator::validationMailAlert($data);
            MailService::changeMailAlert($data['mailAlertTeacher'], $data['mailAlertEducationalManager']);
            new ResponseApi(HttpStatus::NO_CONTENT)->done();
            return;
        }
        catch (\Exception $e) {
            Notification::addNotification(NotificationType::Error, $e->getMessage());
        }
        catch (NestedValidationException $e) {
            foreach ($e->getMessages() as $message) {
                Notification::addNotification(NotificationType::Error, $message);
            }
        }
        new ResponseApi(HttpStatus::BAD_REQUEST)->done();
    }
}