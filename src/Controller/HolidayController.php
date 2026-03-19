<?php
// FILE: src/Controller/HolidayController.php
namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Service\HolidaysService;
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
        $listHoliday = HolidaysService::selectAll();

        Renderer::render(
            '../ViewOLD/listOffPeriod.php',
            'Liste des périodes de congé',
            [
                "listHoliday" => $listHoliday
            ]
        );
    }
}