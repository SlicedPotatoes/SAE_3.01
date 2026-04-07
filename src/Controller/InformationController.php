<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Utils\Renderer;

class InformationController {
    public static function rules(): void {
        Renderer::render('rules.html', 'Règlement intérieur de l\'établissement');
    }

    public static function userManual(): void {
        Renderer::render('userManual.php', 'Manuel d\'utilisation');
    }
}
