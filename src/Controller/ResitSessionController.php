<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\DB\Select\TimeSlotAbsenceSelector;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\ViewModel\ResitSessionListViewModel;
use Uphf\GestionAbsence\ViewModel\TeacherHomeViewModel;

class ResitSessionController
{
    public static function show($params): ControllerData
    {
        // Vérification de la connection de l'utilisateur
        if (!AuthManager::isLogin()) {
            header("Location: /");
            exit();
        }
        // Vérification de la permission de l'utilisateur
        if (!AuthManager::isRole(AccountType::EducationalManager) &&
          !AuthManager::isRole(AccountType::Secretary))
        {
            return ControllerData::get403();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $dateStartFilter = $_POST['dateStartFilter'] ?? null;
            $dateEndFilter   = $_POST['dateEndFilter'] ?? null;
            $dateStartFilter = ($dateStartFilter !== '') ? $dateStartFilter : null;
            $dateEndFilter   = ($dateEndFilter !== '') ? $dateEndFilter : null;
        }
        else
        {
            $dateStartFilter = null;
            $dateEndFilter   = null;
        }

        $filters = array(
          'examFilter' => true,
          'dateStartFilter' => $dateStartFilter,
          'dateEndFilter' => $dateEndFilter,
        );

        $account = AuthManager::getAccount();
        $periods = TimeSlotAbsenceSelector::selectTimeSlotAbsence(
          null,
          true,
          $dateStartFilter,
          $dateEndFilter
        );

        return new ControllerData(
          "/View/resitSessionList.php",
          "Rattrapage",
          new ResitSessionListViewModel(
            $periods,
            $filters
          )
        );
    }
}