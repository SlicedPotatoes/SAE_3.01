<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Model\Notification\Notification;
use Uphf\GestionAbsence\Model\Notification\NotificationType;
use Uphf\GestionAbsence\Service\PredifinedCommentService;
use Uphf\GestionAbsence\Utils\ResponseApi\HttpStatus;
use Uphf\GestionAbsence\Utils\ResponseApi\ResponseApi;

class PredefinedCommentControllerApi {

    public static function postPredefinedComment(){

        $data = json_decode(file_get_contents('php://input'), true);
        $textComment = $data["textComment"];
        PredifinedCommentService::commentInsertor($textComment);

        Notification::addNotification(NotificationType::Success, "Commentaire ajouté avec succès");

        new ResponseApi(HttpStatus::NO_CONTENT)->done();

    }

    public static function deletePredefinedComment(array $params){
        PredifinedCommentService::commentDelete($params['id']);
        Notification::addNotification(NotificationType::Success, "Commentaire supprimé avec succès");

        new ResponseApi(HttpStatus::NO_CONTENT)->done();
    }

    public static function putPredefinedComment(array $params){
        $data = json_decode(file_get_contents('php://input'), true);
        $textComment = $data["textComment"];
        PredifinedCommentService::commentUpdate($params['id'], $textComment);

        Notification::addNotification(NotificationType::Success, "Commentaires modifié avec succès");
        new ResponseApi(HttpStatus::NO_CONTENT)->done();
    }

}