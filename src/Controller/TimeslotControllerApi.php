<?php

namespace Uphf\GestionAbsence\Controller;

use Respect\Validation\Exceptions\NestedValidationException;
use Uphf\GestionAbsence\Model\Notification\Notification;
use Uphf\GestionAbsence\Model\Notification\NotificationType;
use Uphf\GestionAbsence\Service\TimeslotService;
use Uphf\GestionAbsence\Utils\ResponseApi\HttpStatus;
use Uphf\GestionAbsence\Utils\ResponseApi\ResponseApi;
use Uphf\GestionAbsence\Validator\TimeslotValidator;

class TimeslotControllerApi {

    public static function getTimeslots(): void {
        try {
            TimeslotValidator::validationGetTimeslots($_GET);

            $timeslots = TimeslotService::getListTimeSlotWithFilter(
                $_GET['idTeacher'],
                $_GET['examFilter'],
                $_GET['dateStartFilter'],
                $_GET['dateEndFilter']
            );
            new ResponseApi(HttpStatus::OK, $timeslots)->done();
        } catch (NestedValidationException $e) {
            foreach ($e->getMessages() as $message) {
                Notification::addNotification(NotificationType::Error, $message);
            }

            new ResponseApi(HttpStatus::BAD_REQUEST)->done();
        }
    }
}