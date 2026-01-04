<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\DB\Select\StudentSelector;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\Model\Notification\Notification;
use Uphf\GestionAbsence\Model\Notification\NotificationType;
use Uphf\GestionAbsence\ViewModel\StudentStatisticsViewModel;

class StudentStatisticsController
{

    public static function show(array $params): ControllerData
    {
        // Utilisateur non connecté, rediriger vers /
        if(!AuthManager::isLogin()) {
            header("Location: /");
            exit();
        }

        // Si l'utilisateur n'est pas un Responsable Pédagogique il est redirigé vers une page 403
        if(!AuthManager::isRole(AccountType::EducationalManager)) {
            return ControllerData::get403();
        }

        $student = StudentSelector::getStudentById($params['id']);

        // Si il n'y a pas d'étudiant avec l'id, l'utilisateur est redigiré vers une page 404
        if($student === null) {
            Notification::addNotification(NotificationType::Error, "L'étudiant demandé n'existe pas");
            return ControllerData::get404();
        }

        return new ControllerData(
            "/View/generalStatistics.php",
            "Statistiques",
            new StudentStatisticsViewModel()
        );
    }
}