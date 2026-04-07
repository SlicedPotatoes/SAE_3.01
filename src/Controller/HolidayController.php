<?php
// FILE: src/Controller/HolidayController.php
namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Utils\Renderer;

/**
 * Controller pour la gestion des périodes de congé
 *
 * - GET /periode-de-vacances -> showHoliday()
 */
class HolidayController
{
    /**
     * Affiche la vue de configuration des périodes de vacance
     *
     * @return void
     */
    public static function showHoliday(): void
    {
        Renderer::render(
            'listOffPeriod.php',
            'Périodes de vacances',
            []
        );
    }
}