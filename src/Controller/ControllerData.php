<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\ViewModel\BaseViewModel;
use Uphf\GestionAbsence\ViewModel\ErrorViewModel;

/**
 * Les données renvoyées par les différents controller au renderer.
 *
 * Les données renvoyées sont:
 * - Le chemin vers le fichier de la View à afficher
 * - Le titre affiché dans le <title>
 * - Les données utilisé par la view
 */
readonly class ControllerData {
    public string $view;
    public string $title;
    public BaseViewModel $data;

    public function __construct(string $view, string $title, BaseViewModel $data) {
        $this->view = $view;
        $this->title = $title;
        $this->data = $data;
    }

    /**
     * ControllerData pour afficher une page 404
     * @return ControllerData
     */
    public static function get404(): ControllerData {
        return new ControllerData(
            "/View/error.php",
            "404 Not Found",
            new ErrorViewModel(
                404,
                "Oops! Vous vous êtes perdu !",
                "La page à laquelle vous souhaitez accéder n'existe plus ou a été changé."
            )
        );
    }

    /**
     * ControllerData pour afficher une page 403
     * @return ControllerData
     */
    public static function get403(): ControllerData {
        return new ControllerData(
            "/View/error.php",
            "403 Forbidden",
            new ErrorViewModel(
                403,
                "Oops! Vous n'avez pas le droit d'être ici !",
                "Vous n'avez pas la permission de consulter cette page."
            )
        );
    }
}