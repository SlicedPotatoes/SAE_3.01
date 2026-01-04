<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\ViewModel\GeneralStatisticsViewModel;

class GeneralStatisticsController
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

        return new ControllerData(
            "/View/generalStatistics.php",
            "Statistiques",
            new GeneralStatisticsViewModel()
        );
    }
}