<?php
// FILE: src/Controller/HolidayContoller.php
namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\DB\Insert\HolidaysInsertor;
use Uphf\GestionAbsence\Model\DB\Select\HolidaySelector;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\ViewModel\BaseViewModel;
use Uphf\GestionAbsence\ViewModel\HolidayPeriodViewModel;

class HolidayContoller
{
    public static function show(array $params = []): ControllerData {

        if(!AuthManager::isLogin()) {
            header("Location: /");
            exit();
        }

        if(!AuthManager::isRole(AccountType::EducationalManager)) {
            return ControllerData::get403();
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            if(isset($_POST['action']) && $_POST['action'] == 'insert') {
                $start = $_POST["startDate"];
                $end = $_POST["endDate"];
                $name = $_POST["periodName"];

                HolidaysInsertor::insert($start, $end, $name);

            }
        }

        $periods = HolidaySelector::getHolidays();


        return new ControllerData(
            '/View/listHolidayPeriod.php',
            'Liste des périodes de congé',
            new HolidayPeriodViewModel($periods),
        );
    }
}
