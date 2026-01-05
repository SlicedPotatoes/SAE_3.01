<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\ViewModel\BaseViewModel;

class RulesController {
    public static function show(): ControllerData
    {
        return new ControllerData(
            '/View/rules.php',
            "Règlement intérieur de l’établissement",
            new BaseViewModel()
        );
    }

}