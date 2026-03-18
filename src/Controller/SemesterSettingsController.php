<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Service\SemesterService;

/**
 * Controller pour la gestion des semestres
 */
class SemesterSettingsController
{
    /**
     * Afficher la page de gestion des semestres
     *
     * @return void
     */
    public static function showSemesterSettings(): void
    {
        $semesters = SemesterService::getCurrentSemesters();
        //$viewModel = new SemesterSettingsViewModel($semesters);

        /*return new ControllerData(
            '/View/semesterSettings.php',
            'Définir les semestres',
            $viewModel
        );*/
    }
}