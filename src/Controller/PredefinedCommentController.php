<?php
namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\DB\Select\CommentSelector;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\ViewModel\JustificationListViewModel;
use Uphf\GestionAbsence\ViewModel\PredefinedCommentViewModel;


class PredefinedCommentController {

    public static function show(): ControllerData{
        if (!AuthManager::isLogin()) {
            header("location: /");
            exit();
        }
        if (!AuthManager::isRole(AccountType::EducationalManager)) {
            return ControllerData::get403();
        }

        $comments = CommentSelector::getAllComments();



        return new ControllerData(
            "/View/predefinedComments",
            "Liste des commentaires prédéfinis",
            new PredefinedCommentViewModel(
                $comments,
                AuthManager::getRole(),
            )
        );

    }
}