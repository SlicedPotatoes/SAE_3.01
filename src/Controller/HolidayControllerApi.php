<?php
// FILE: src/Controller/HolidayController.php
namespace Uphf\GestionAbsence\Controller;

use DateTime;
use InvalidArgumentException;
use Uphf\GestionAbsence\Database\Insert\OffPeriodInsertor;
use Uphf\GestionAbsence\Database\Select\OffPeriodSelector;
use Uphf\GestionAbsence\Database\Update\OffPeriodUpdater;
use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\Model\Notification\Notification;
use Uphf\GestionAbsence\Model\Notification\NotificationType;
use Uphf\GestionAbsence\Service\HolidaysService;
use Uphf\GestionAbsence\Utils\ResponseApi\HttpStatus;
use Uphf\GestionAbsence\Utils\ResponseApi\ResponseApi;
use Uphf\GestionAbsence\ViewModel\OffPeriodViewModel;

/**
 *  Controller pour la gestion des périodes de vacances
 */

/**
 * Controller pour la gestion des périodes de congé
 *
 * - holidayGet    -> GET    /holiday     (API get)
 * - holidayPost   -> POST   /holiday     (API insert)
 * - holidayDelete -> DELETE /holiday/{id} (API delete)
 * - holidayUpdate -> PUT    /holiday/{id} (API update)
 */
class HolidayControllerApi
{
    /**
     * Get /holidays
     * Permet de répondre à une demande d'envoie de toutes les Holidays
     */
    public static function getHolidays(): void
    {
        $holidays = HolidaysService::selectAll();

        new ResponseApi(
            HttpStatus::OK,
            $holidays
        )->done();
    }

    /**
     * POST /holidays
     * Permet d'inséré un nouveau Holiday
     */
    public static function postHoliday(): void
    {
        $data = json_decode(file_get_contents("php://input"), true);

        $start = $data['startDate'] ?? null;
        $end = $data['endDate'] ?? null;
        $name = $data['label'] ?? null;

        if (!$start || !$end || !$name) {
            Notification::addNotification(
                NotificationType::Error,
                "Tous les champs n'ont pas étaient remplis");
            new ResponseApi(
                HttpStatus::BAD_REQUEST,
                (array)"Tous les champs n'ont pas étaient remplis"
            )->done();
        }

        try {
            HolidaysService::insert($start, $end, $name);
            Notification::addNotification(
                NotificationType::Success,
                "Nouvelle période de vacances créer avec succès");
            new ResponseApi(
                HttpStatus::CREATED
            )->done();
        } catch (InvalidArgumentException $e) {
            Notification::addNotification(
                NotificationType::Error,
                "Erreur lors de la création de la période de vacances, réessayez plus tard"
            );
            new ResponseApi(
                HttpStatus::BAD_REQUEST,
                (array)$e->getMessage()
            )->done();
        }
    }

    /**
     * PUT /holidays/{id:int]}
     * Permet de mettre à jour une période de vacance
     */
    public static function putHoliday(array $params): void {
        $id = $params['id'] ?? null;

        // Récupération des données
        $data = json_decode(file_get_contents('php://input'), true ?? []);

        $start = $data['startDate'] ?? null;
        $end = $data['endDate'] ?? null;
        $name = $data['label'] ?? null;

        // Vérifie que tous les champs obligatoires sont présents
        if (!$id || !$start || !$end || !$name) {
            Notification::addNotification(
                NotificationType::Error,
                "Tous les champs n'ont pas étaient remplis");
            new ResponseApi(
                HttpStatus::BAD_REQUEST,
                (array)"Tous les champs n'ont pas étaient remplis"
            )->done();
        }

        // Appelle le service métier pour faire la mise à jour
        try {
            HolidaysService::update($id, $start, $end, $name);
            Notification::addNotification(
                NotificationType::Success,
                "Mise à jours de la période de vacances avec succès");
            new ResponseApi(
                HttpStatus::CREATED
            )->done();
        } catch (InvalidArgumentException $e) {
            Notification::addNotification(
                NotificationType::Error,
                "Erreur lors de la mise à jours de la période de vacances, réessayez plus tard"
            );
            new ResponseApi(
                HttpStatus::NOT_FOUND,
                (array)$e->getMessage()
            )->done();
        }
    }

    /**
     * DELETE /holidays/{id:int}
     * Permet la suppression d'une période de vacance
     */
    public static function deleteHoliday(array $params): void{
        $id = $params['id'] ?? null;

        if (!$id) {
            new ResponseApi(
                HttpStatus::BAD_REQUEST,
                array("result" => "Object not found")
            )->done();
        }

        try {
            HolidaysService::delete($id);
            Notification::addNotification(
                NotificationType::Success,
                "Suppression de la période de vacances avec succès");
            new ResponseApi(
                HttpStatus::NO_CONTENT
            )->done();
        } catch (InvalidArgumentException $e) {
            Notification::addNotification(
                NotificationType::Error,
                "Erreur lors de la suppression de la période de vacances, réessayez plus tard"
            );
            new ResponseApi(
                HttpStatus::NOT_FOUND,
                (array)$e->getMessage()
            )->done();
        }
    }
}