<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Utils\Renderer;

/**
 * Controller pour la gestion du guide de l'utilisateur pour l'application et pour le règlement intérieur
 */
class InformationController {
    public static function rules(): void {
        Renderer::render('../ViewOLD/rules.php', 'Règlement intérieur de l\'établissement');
    }

    public static function userManual(): void {
        Renderer::render('../ViewOLD/userManual.php', 'Manuel d\'utilisation');
    }
}