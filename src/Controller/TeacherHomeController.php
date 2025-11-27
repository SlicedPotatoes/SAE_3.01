<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\DB\Select\TimeSlotAbsenceSelector;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\ViewModel\BaseViewModel;
use Uphf\GestionAbsence\ViewModel\TeacherHomeViewModel;

class TeacherHomeController {

    public static function show(): ControllerData {
        // Vérification de la connection de l'utilisateur
        if (!AuthManager::isLogin()) {
            header("Location: /");
            exit();
        }
        // Vérification de la permission de l'utilisateur
        if (!AuthManager::isRole(AccountType::Teacher))
        {
            return ControllerData::get403();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $examFilter      = isset($_POST['examFilter']);
            $dateStartFilter = $_POST['dateStartFilter'] ?? null;
            $dateEndFilter   = $_POST['dateEndFilter'] ?? null;

            $dateStartFilter = ($dateStartFilter !== '') ? $dateStartFilter : null;
            $dateEndFilter   = ($dateEndFilter !== '') ? $dateEndFilter : null;
        }
        else
        {
            $examFilter      = false;
            $dateStartFilter = null;
            $dateEndFilter   = null;
        }

        $filters = array(
          'examFilter' => $examFilter,
          'dateStartFilter' => $dateStartFilter,
          'dateEndFilter' => $dateEndFilter,
        );

        $account = AuthManager::getAccount();
        $periods = TimeSlotAbsenceSelector::selectTimeSlotAbsence(
          $account->getIdAccount(),
          $examFilter,
          $dateStartFilter,
          $dateEndFilter
        );

        return new ControllerData(
            "View/teacherHome.php",
            "Tableau de bord Professeur",
            new TeacherHomeViewModel(
              $periods,
              $filters,
              AuthManager::getAccount()->getFirstName() . " " . AuthManager::getAccount()->getLastName()
            )
        );
    }

}