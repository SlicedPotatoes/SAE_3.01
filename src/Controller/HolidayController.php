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
 *  Controller pour la gestion des périodes de vacances
 */

/**
 * Controller pour la gestion des périodes de congé
 *
 * - holidayGet    -> GET    /holiday     (Vue)
 */
class HolidayController
{
    /**
     * @param aray $params
     * @return ControllerData
     */
    public static function showHoliday(): ControllerData
    {
        $listHoliday = HolidaysService::selectAll();

        return new ControllerData(
            '/View/listOffPeriod.php',
            'Liste des périodes de congé',
            new OffPeriodViewModel($listHoliday),
        );
    }
}