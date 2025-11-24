<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\ViewModel\BaseViewModel;

class ChangePasswordController{


    public static function show(){
        return new ControllerData(
            "/View/changePassword.php",
            "Changer le mot de passe",
            new BaseViewModel()
        );
    }
}

