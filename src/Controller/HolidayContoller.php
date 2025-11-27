<?php
// FILE: src/Controller/HolidayContoller.php
namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Model\DB\Update\HolidayUpdater;
use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\DB\Insert\HolidaysInsertor;
use Uphf\GestionAbsence\Model\DB\Select\HolidaySelector;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
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

        // --- Traiter les POST d'abord (insert / delete / update), puis rediriger (PRG)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            $action = $_POST['action'];

            if ($action === 'insert') {
                $start = $_POST["startDate"] ?? '';
                $end = $_POST["endDate"] ?? '';
                $name = $_POST["periodName"] ?? '';
                HolidaysInsertor::insert($start, $end, $name);

                header('Location: ' . $_SERVER['REQUEST_URI']);
                exit();
            }

            if ($action === 'delete') {
                $id = isset($_POST["id"]) ? (int)$_POST["id"] : 0;
                if ($id > 0) {
                    HolidayUpdater::delete($id);
                }

                header('Location: ' . $_SERVER['REQUEST_URI']);
                exit();
            }

            if ($action === 'update') {
                $id = isset($_POST["id"]) ? (int)$_POST["id"] : 0;
                $start = $_POST["startDate"] ?? '';
                $end = $_POST["endDate"] ?? '';
                $name = $_POST["periodName"] ?? '';
                if ($id > 0) {
                    HolidayUpdater::update($id, $start, $end, $name);
                }

                header('Location: ' . $_SERVER['REQUEST_URI']);
                exit();
            }
        }

        // --- Maintenant récupérer les périodes pour l'affichage
        $periods = HolidaySelector::getHolidays();

        return new ControllerData(
            '/View/listHolidayPeriod.php',
            'Liste des périodes de congé',
            new HolidayPeriodViewModel($periods),
        );
    }
}
