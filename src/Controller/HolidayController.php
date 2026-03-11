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
use Uphf\GestionAbsence\ViewModel\OffPeriodViewModel;

/**
 *  Controller pour la gestion des périodes de congé
 */

/**
 * Controller pour la gestion des périodes de congé
 *
 * - holidayGet    -> GET    /holiday     (Vue)
 * - holidayPost   -> POST   /holiday     (API / traitement création)
 * - holidayDelete -> DELETE /holiday/{id} (API suppression)
 * - holidayUpdate -> PUT    /holiday/{id} (API mise à jour)
 */
class HolidayController
{
    /**
     * @param aray $params
     * @return ControllerData
     */
    public static function showHoliday(): ControllerData
    {

        // On récupère toutes les périodes de vacances déjà présentes dans la BDD
        $listHoliday = HolidaysService::selectAll();

        return new ControllerData(
            '/View/listOffPeriod.php',
            'Liste des périodes de congé',
            new OffPeriodViewModel($listHoliday),
        );
    }


    // TODO : Rework les contoller API
    /**
     * @param array $params
     * @return void
     */
    public static function postHoliday(): void {

        $start = $_POST['startDate'] ?? null;
        $end = $_POST['endDate'] ?? null;
        $name = $_POST['periodName'] ?? null;

        if (!$start || !$end || !$name) {
            Notification::addNotification(
                NotificationType::Error,
                "Un des champs obligatoire n'a pas été fournis"
            );
            http_response_code(400);
            return;
        }

        try {
            HolidaysService::insert($start, $end, $name);
            http_response_code(201);
        } catch (InvalidArgumentException $e) {
            Notification::addNotification(NotificationType::Error, $e->getMessage());
            http_response_code(400);
        }

    }

    /**
     * @param array $params
     * @return void
     */
    // TODO : demander a Chat comment récupérer l'ID avec delete en PHP
    public static function deleteHoliday(): void{

        $data = json_decode(file_get_contents('php//input'), true ?? []);

        $id = $data['id'] ?? null;

        try {
            HolidaysService::delete($id);
            http_response_code(204);
        }catch (InvalidArgumentException $e){
            Notification::addNotification(
                NotificationType::Error,
                $e->getMessage());
        }
    }

    // TODO : Poser des question à Kevin pour la récupération des données
    public static function putHoliday(): void {

        // Récupération des données
        $data = json_decode(file_get_contents('php//input'), true ?? []);

        $id = $data['id'] ?? null;
        $start = $data['startDate'] ?? null;
        $end = $data['endDate'] ?? null;
        $name = $data['periodName'] ?? null;

        // Vérifie que tous les champs obligatoires sont présents
        if (!$start || !$end || !$name) {
            Notification::addNotification(
                NotificationType::Error,
                "Un des champs obligatoire n'a pas été fournis"
            );
            http_response_code(400);
            return;
        }

        // Appelle le service métier pour faire la mise à jour
        try {
            HolidaysService::update($id, $start, $end, $name);
            http_response_code(200);
        } catch (InvalidArgumentException $e) {
            // Gère les erreurs de validation métier (ex: dates incohérentes)
            Notification::addNotification(
                NotificationType::Error,
                $e->getMessage()
            );
            http_response_code(400);
        }
    }

}