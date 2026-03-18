<?php
namespace Uphf\GestionAbsence\Controller;

use InvalidArgumentException;
use Respect\Validation\Exceptions\NestedValidationException;
use Uphf\GestionAbsence\Exception\EntityNotFoundException;
use Uphf\GestionAbsence\Model\Notification\Notification;
use Uphf\GestionAbsence\Model\Notification\NotificationType;
use Uphf\GestionAbsence\Service\HolidaysService;
use Uphf\GestionAbsence\Utils\ResponseApi\HttpStatus;
use Uphf\GestionAbsence\Utils\ResponseApi\ResponseApi;
use Uphf\GestionAbsence\Validator\HolidayValidator;

/**
 * Controller pour la gestion des périodes de congé
 *
 * - GET /holidays -> getHolidays()
 * - POST /holidays -> postHoliday()
 * - PUT /holidays/{id:int} -> putHoliday()
 * - DELETE /holidays/{id:int} -> deleteHoliday()
 */
class HolidayControllerApi
{
    /**
     * Permet de répondre à une demande d'envoie de toutes les Holidays
     *
     * @return void
     */
    public static function getHolidays(): void
    {
        $holidays = HolidaysService::selectAll();

        new ResponseApi(HttpStatus::OK, $holidays)->done();
    }

    /**
     * Permet d'inséré un nouveau Holiday
     *
     * @return void
     */
    public static function postHoliday(): void
    {
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            HolidayValidator::validationHoliday($data);
            HolidaysService::insert($data['startDate'], $data['endDate'], $data['label']);

            Notification::addNotification(NotificationType::Success, "Nouvelle période de vacances créer avec succès");
            new ResponseApi(HttpStatus::CREATED,)->done();
            return;
        }
        catch (NestedValidationException $e) {
            foreach ($e->getMessages() as $message) {
                Notification::addNotification(NotificationType::Error, $message);
            }
        }
        catch (InvalidArgumentException $e) {
            Notification::addNotification(NotificationType::Error, "La date de début dois être inférieure a la date de fin");
        }
        new ResponseApi(HttpStatus::BAD_REQUEST)->done();
    }

    /**
     * Permet de mettre à jour une période de vacances
     *
     * @param array $params
     * @return void
     */
    public static function putHoliday(array $params): void {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            HolidayValidator::validationHoliday($data);

            HolidaysService::update($params['id'], $data['startDate'], $data['endDate'], $data['label']);
            Notification::addNotification(NotificationType::Success, "Mise à jours de la période de vacances avec succès");

            new ResponseApi(HttpStatus::CREATED)->done();
            return;
        }
        catch (NestedValidationException $e) {
            foreach ($e->getMessages() as $message) {
                Notification::addNotification(NotificationType::Error, $message);
            }
        }
        catch (InvalidArgumentException $e) {
            Notification::addNotification(NotificationType::Error, "La date de début dois être inférieure a la date de fin");
        }
        catch (EntityNotFoundException $e) {
            Notification::addNotification(NotificationType::Error, "La période de vacances n'a pas été trouvé");
        }
        new ResponseApi(HttpStatus::BAD_REQUEST)->done();
    }

    /**
     * Permet la suppression d'une période de vacances
     *
     * @param array $params
     * @return void
     */
    public static function deleteHoliday(array $params): void{
        try {
            HolidaysService::delete($params['id']);
            Notification::addNotification(NotificationType::Success, "Suppression de la période de vacances avec succès");
            new ResponseApi(HttpStatus::NO_CONTENT)->done();
        }
        catch (InvalidArgumentException $e) {
            Notification::addNotification(NotificationType::Error, "La période de vacances n'a pas été trouvé");
            new ResponseApi(HttpStatus::NOT_FOUND)->done();
        }
    }
}