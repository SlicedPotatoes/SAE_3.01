<?php

namespace Uphf\GestionAbsence\Controller;

use DateTime;
use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\DB\Select\TimeSlotAbsenceSelector;
use Uphf\GestionAbsence\Model\Entity\Absence\Resource;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\ViewModel\DetailPeriodViewModel;

class DetailPeriodController
{
    public static function show($params): ControllerData
    {
        // Utilisateur non connecté, rediriger vers /
        if (!AuthManager::isLogin()) {
            header("Location: /");
            exit();
        }
        // Vérification de la permission de l'utilisateur
        if (AuthManager::isRole(AccountType::Student)) {
            return ControllerData::get403();
        }

        if (
          !isset($_GET['time']) ||
          !isset($_GET['resourceId']) ||
          !isset($_GET['teacher']) ||
          !isset($_GET['group'])
        )
        {
            return ControllerData::get404();
        }

        $time = DateTime::createFromFormat('Y-m-d-H-i', $_GET['time']);
        $resourceId = (int) $_GET['resourceId'];
        $teacherId = (int) $_GET['teacher'];
        $group = ($_GET['group'] === 'nogroup') ? null : $_GET['group'];

        $period = TimeSlotAbsenceSelector::getTimeSlot(
          $time,
          $resourceId,
          $teacherId,
          $group
        );

        if ($period === null) {
            return ControllerData::get404();
        }

        $absences = TimeSlotAbsenceSelector::getAbsenceListByTimeSlotAbsence($period);

            return new ControllerData(
          "/View/detailPeriod.php",
          "Détail crénaux",
          new DetailPeriodViewModel(
            $absences,
            $period->getTime(),
            $period->isExamen(),
            $period->getCourseType(),
            $period->getResource(),
            $period->getGroup(),
            $period->getTeacher()->getLastName() . ' ' . $period->getTeacher()->getFirstName(),
            AuthManager::isRole(AccountType::Teacher)
          )
        );

        return ControllerData::get404();
    }
}