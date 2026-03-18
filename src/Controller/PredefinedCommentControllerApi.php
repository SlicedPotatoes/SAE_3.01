<?php

namespace Uphf\GestionAbsence\Controller;

use Respect\Validation\Exceptions\NestedValidationException;
use Uphf\GestionAbsence\Model\Notification\Notification;
use Uphf\GestionAbsence\Model\Notification\NotificationType;
use Uphf\GestionAbsence\Service\PredifinedCommentService;
use Uphf\GestionAbsence\Utils\ResponseApi\HttpStatus;
use Uphf\GestionAbsence\Utils\ResponseApi\ResponseApi;
use Uphf\GestionAbsence\Validator\PredefinedCommentValidator;

/**
 * Controller pour les commentaires prédéfinis pour le responsable pédagogique lors du traitement d'un justificatif
 */
class PredefinedCommentControllerApi {

    /**
     * POST /api/predefinedComment
     * Permet d'insérer un nouveau commentaire dans la base de données
     *
     * @return void
     */
    public static function postPredefinedComment(): void {
        $data = json_decode(file_get_contents('php://input'), true);

        try {
            PredefinedCommentValidator::validationPostPredefinedComment($data);
            PredifinedCommentService::commentInsertor($data["textComment"]);

            Notification::addNotification(NotificationType::Success, "Commentaire ajouté avec succès");
            new ResponseApi(HttpStatus::NO_CONTENT)->done();
        }
        catch (NestedValidationException $e) {
            foreach($e->getMessages() as $message) {
                Notification::addNotification(NotificationType::Error, $message);
            }
            new ResponseApi(HttpStatus::BAD_REQUEST)->done();
        }
    }

    /**
     * DELETE /api/predefinedComment/{id:int}
     * Permet de supprimer un commentaire dans la base de données
     *
     * @param array $params
     * @return void
     */
    public static function deletePredefinedComment(array $params): void {
        PredifinedCommentService::commentDelete($params['id']);

        Notification::addNotification(NotificationType::Success, "Commentaire supprimé avec succès");
        new ResponseApi(HttpStatus::NO_CONTENT)->done();
    }

    /**
     * PUT /api/predefinedComment/{id:int}
     * Permet de mettre à jour un commentaire dans la base de données
     *
     * @param array $params
     * @return void
     */
    public static function putPredefinedComment(array $params): void {
        $data = json_decode(file_get_contents('php://input'), true);

        try {
            PredefinedCommentValidator::validationPostPredefinedComment($data);
            PredifinedCommentService::commentUpdate($params['id'], $data['textComment']);

            Notification::addNotification(NotificationType::Success, "Commentaires modifié avec succès");
            new ResponseApi(HttpStatus::NO_CONTENT)->done();
            return;
        }
        catch (NestedValidationException $e) {
            foreach($e->getMessages() as $message) {
                Notification::addNotification(NotificationType::Error, $message);
            }
        }

        new ResponseApi(HttpStatus::BAD_REQUEST)->done();
    }

}