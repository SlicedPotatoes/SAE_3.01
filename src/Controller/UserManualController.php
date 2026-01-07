<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\ViewModel\BaseViewModel;
class UserManualController
{
    public static function show(): ControllerData
    {
        return new ControllerData(
            '/View/userManual.php',
            "Manuel d'utilisation",
             new BaseViewModel()
        );
    }
}
