<?php

namespace Uphf\GestionAbsence\Controller\OLD;

use Uphf\GestionAbsence\Controller\ControllerData;

class Routine
{
    public static function launch($params) : ControllerData {
        require_once dirname(__DIR__) . '/Routine/LaunchRoutine.php';
        exit();
    }
}