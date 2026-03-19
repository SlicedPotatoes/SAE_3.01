<?php

namespace Uphf\GestionAbsence\Controller\OLD;

class Routine
{
    public static function launch($params) : void {
        require_once dirname(__DIR__) . '/Routine/LaunchRoutine.php';
    }
}