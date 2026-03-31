<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Utils\Renderer;

/**
 * Controller responsable de l'affichage des vues 404 et 403
 */
class ErrorController {
    /**
     * Affiche la vue 404
     * @return void
     */
    public static function error404(): void {
        http_response_code(404);

        Renderer::render(
            'error.php',
            '404 - Page introuvable',
            [
                'errorCode' => 404,
                'errorMessage1' => 'Oops! Vous vous êtes perdu !',
                'errorMessage2' => "La page à laquelle vous souhaitez accéder n'existe plus ou a été changé."
            ]
        );
    }

    /**
     * Affiche la vue 403
     * @return void
     */
    public static function error403(): void {
        http_response_code(403);

        Renderer::render(
            'error.php',
            '403 - Accès refusé',
            [
                "errorCode" => 403,
                "errorMessage1" => "Oops! Vous n'avez pas le droit d'être ici !",
                "errorMessage2" => "Vous n'avez pas la permission de consulter cette page."
            ]
        );
    }
}