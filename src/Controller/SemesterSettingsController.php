<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Database\Select\SemesterSelector;
use Uphf\GestionAbsence\Service\SemesterService;
use Uphf\GestionAbsence\ViewModel\SemesterSettingsViewModel;

/**
 * Controller pour la gestion des semestres
 */
class SemesterSettingsController
{
    /**
     * Afficher la page de gestion des semestres
     *
     * @return ControllerData
     */
    public static function showSemesterSettings(): ControllerData
    {
        $semesters = SemesterService::getCurrentSemesters();
        $viewModel = new SemesterSettingsViewModel($semesters);

        return new ControllerData(
            '/View/semesterSettings.php',
            'Définir les semestres',
            $viewModel
        );
    }
}