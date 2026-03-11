<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Service\PredifinedCommentService;
use Uphf\GestionAbsence\ViewModel\PredefinedCommentViewModel;

/**
 * Controller pour les commentaires prédéfinis
 *
 */
class PredefinedCommentController
{
    /**
     * Affiche la liste des commentaires prédéfinis
     *
     * @return ControllerData
     */
    public static function predefinedCommentGet(): ControllerData
    {
        $comments = PredifinedCommentService::commentSelectorAll();

        return new ControllerData(
            '/View/predefinedComments.php',
            'Liste des commentaires prédéfinis',
            new PredefinedCommentViewModel(
                AuthManager::getRole(),
                $comments
            )
        );
    }
}
