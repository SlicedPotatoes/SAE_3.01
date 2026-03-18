<?php

namespace Uphf\GestionAbsence\Utils;

/**
 * Classe responsable de rendre les vues
 */
class Renderer {
    private static string $viewsDirectory = __DIR__ . '/../View/';

    /**
     * Permet le rendu d'une view
     *
     * @param string $view      Nom du fichier de la view
     * @param string $title     Titre de la page
     * @param array $data       Donnée a envoyé à la vue, sous forme de tableau associatif
     * @param string $layout    Nom du layout (squelette)
     * @return void
     */
    public static function render(string $view, string $title, array $data = [], string $layout = 'main.php'): void {
        ob_start();
        require Renderer::$viewsDirectory . $view;
        $content = ob_get_clean();

        require Renderer::$viewsDirectory . 'Layouts/' . $layout;
    }

    /**
     * Afficher une vue 403
     * @return void
     */
    public static function render403(): void {
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

    /**
     * Afficher une vue 404
     * @return void
     */
    public static function render404(): void {
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
}