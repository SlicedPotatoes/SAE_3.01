<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\ViewModel\SemesterSettingsViewModel;

/**
 * Controller pour la gestion des paramètres de semestre
 */
class SemesterSettingsController {
    /**
     * Si l'utilisateur n'est pas connecté => Rediriger vers login
     *
     * Si l'utilisateur n'a pas les permissions de voir la page => 403
     *
     * @param array $params
     * @return ControllerData
     */
    public static function show(array $params = []): ControllerData {
        // Si pas login -> Rediriger login
        if(!AuthManager::isLogin()) {
            header("Location: /");
            exit();
        }

        // Si pas RP -> Rediriger 403
        if(!AuthManager::isRole(AccountType::EducationalManager)) {
            return ControllerData::get403();
        }

        // TODO: Récupérer les semestres depuis la BDD
        $viewModel = new SemesterSettingsViewModel();

        return new ControllerData(
            '/View/semesterSettings.php',
            'Définir les semestres',
            $viewModel,
        );
    }
}
