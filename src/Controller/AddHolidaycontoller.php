<?php
// FILE: src/Controller/AddHolidaycontoller.php
namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\ViewModel\BaseViewModel;

class AddHolidaycontoller
{
    public static function show(array $params = []): ControllerData
    {
        return new ControllerData(
            '/View/listHolidayPeriod.php',
            'Liste des périodes de congé',
            new BaseViewModel()
        );
    }
}
