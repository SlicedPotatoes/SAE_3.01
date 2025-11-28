<?php

namespace Uphf\GestionAbsence\Controller;

class Routine
{
    public static function launch($params) : ControllerData {
        require_once dirname(__DIR__) . '/Routine/LaunchRoutine.php';
        exit();
    }
}