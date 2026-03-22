<?php

namespace Uphf\GestionAbsence\Utils;

/**
 * Classe responsable de rendre les vues
 */
class Renderer {
    private static string $viewsDirectory = __DIR__ . '/../View/';
    public static array $assets;

    /**
     * Permet d'injecter un asset dans le layout
     *
     * Cas d'utilisation :
     * Une view a besoin d'un script js / style css spécifique, qui n'est pas commun à toutes les pages
     *
     * Utilisation pour JS :
     * Renderer::pushAsset('script', '<script src="/script/mon-script.js"></script>');
     *
     * Utilisation pour CSS :
     * Renderer::pushAsset('head', '<link rel="stylesheet" href="/style/mon-style.css">');
     *
     * @param string $type head ou script
     * @param string $content balise a injecter dans le layout
     * @return void
     */
    public static function pushAsset(string $type, string $content): void {
        self::$assets[$type][] = $content;
    }

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