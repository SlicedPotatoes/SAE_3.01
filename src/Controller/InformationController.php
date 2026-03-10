<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\ViewModel\BaseViewModel;

class InformationController {
    public static function rules(): ControllerData {
        return new ControllerData(
            '/View/rules.php',
            "Règlement intérieur de l’établissement",
            new BaseViewModel()
        );
    }

    public static function userManual(): ControllerData {
        return new ControllerData(
            '/View/userManual.php',
            "Manuel d'utilisation",
            new BaseViewModel()
        );
    }
}