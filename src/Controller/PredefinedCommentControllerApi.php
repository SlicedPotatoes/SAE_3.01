<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Model\Notification\Notification;
use Uphf\GestionAbsence\Model\Notification\NotificationType;
use Uphf\GestionAbsence\Service\PredifinedCommentService;
use Uphf\GestionAbsence\Utils\ResponseApi\HttpStatus;
use Uphf\GestionAbsence\Utils\ResponseApi\ResponseApi;

/**
 * Controller pour les commentaires prédéfinis pour le responsable pédagogique lors du traitement d'un justificatif
 */
class PredefinedCommentControllerApi {

    /**
     * POST /api/predefinedComment
     * Permet d'insérer un nouveau commentaire dans la base de données
     */
    public static function postPredefinedComment(){

        $data = json_decode(file_get_contents('php://input'), true);
        $textComment = $data["textComment"];
        PredifinedCommentService::commentInsertor($textComment);

        Notification::addNotification(NotificationType::Success, "Commentaire ajouté avec succès");

        new ResponseApi(HttpStatus::NO_CONTENT)->done();

    }

    /**
     * PUT /api/predefinedComment/{id:int}
     * Permet de mettre à jours un commentaire dans la base de données
     */
    public static function deletePredefinedComment(array $params){
        PredifinedCommentService::commentDelete($params['id']);
        Notification::addNotification(NotificationType::Success, "Commentaire supprimé avec succès");

        new ResponseApi(HttpStatus::NO_CONTENT)->done();
    }

    /**
     * DELETE /api/predefinedComment/{id:int}
     * Permet de supprimer un commentaire dans la base de données
     */
    public static function putPredefinedComment(array $params){
        $data = json_decode(file_get_contents('php://input'), true);
        $textComment = $data["textComment"];
        PredifinedCommentService::commentUpdate($params['id'], $textComment);

        Notification::addNotification(NotificationType::Success, "Commentaires modifié avec succès");
        new ResponseApi(HttpStatus::NO_CONTENT)->done();
    }

}