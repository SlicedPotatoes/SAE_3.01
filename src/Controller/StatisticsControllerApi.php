<?php

namespace Uphf\GestionAbsence\Controller;

use Respect\Validation\Exceptions\NestedValidationException;
use Uphf\GestionAbsence\Exception\EntityNotFoundException;
use Uphf\GestionAbsence\Model\Notification\Notification;
use Uphf\GestionAbsence\Model\Notification\NotificationType;
use Uphf\GestionAbsence\Service\StatisticService;
use Uphf\GestionAbsence\Utils\ResponseApi\HttpStatus;
use Uphf\GestionAbsence\Utils\ResponseApi\ResponseApi;
use Uphf\GestionAbsence\Validator\StatisticsValidator;


class StatisticsControllerApi {
    public static function getStatistics(): void {
        try {
            StatisticsValidator::validationGetStatistics($_GET);
            $statistics = StatisticService::getStatistic($_GET);

            new ResponseApi(HttpStatus::OK, $statistics)->done();
        }
        catch (NestedValidationException $e) {
            foreach ($e->getMessages() as $message) {
                Notification::addNotification(NotificationType::Error, $message);
            }
        }
        catch (EntityNotFoundException $e) {
            Notification::addNotification(NotificationType::Error, "L'étudiant demandé n'existe pas.");

        }

        new ResponseApi(HttpStatus::BAD_REQUEST)->done();
    }

}