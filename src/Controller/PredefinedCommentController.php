<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Utils\Renderer;

/**
 * Controller pour les commentaires prédéfinis
 *
 */
class PredefinedCommentController
{
    /**
     * Affiche la liste des commentaires prédéfinis
     *
     * @return void
     */
    public static function showPredefinedComment(): void
    {
        Renderer::render(
            'predefinedComments.php',
            'Commentaires prédéfinis',
            []
        );
    }
}
