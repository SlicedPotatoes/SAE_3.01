<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Service\PredifinedCommentService;
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
        $comments = PredifinedCommentService::commentSelectorAll();

        Renderer::render(
            '../ViewOLD/predefinedComments.php',
            'Liste des commentaires prédéfinis',
            [
                'comments' => $comments
            ]
        );
    }
}
