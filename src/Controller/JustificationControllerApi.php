<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Database\Select\SelectBuilder\SortOrder;
use Uphf\GestionAbsence\Exception\AbsenceNotProvidedException;
use Uphf\GestionAbsence\Exception\CommentEducationalManagerNotProvidedException;
use Uphf\GestionAbsence\Exception\EntityNotFoundException;
use Uphf\GestionAbsence\Model\Entity\Justification\StateJustif;
use Uphf\GestionAbsence\Model\Notification\Notification;
use Uphf\GestionAbsence\Model\Notification\NotificationType;
use Uphf\GestionAbsence\Model\Validation\ProcessJustificationValidator;
use Uphf\GestionAbsence\Service\JustificationService;
use Uphf\GestionAbsence\Utils\ResponseApi\HttpStatus;
use Uphf\GestionAbsence\Utils\ResponseApi\ResponseApi;

/**
 * Controller api pour les justificatifs
 *
 *  - GET /api/justifications -> getJustificationList()
 *  - PUT /api/justifications/{id} -> putDetailJustification()
 */
class JustificationControllerApi {

    /**
     * Récupérer une liste de justificatifs avec des filtres
     *
     * @return void
     */
    public static function getJustificationList(): void {
        $filters = $_GET['filters'] ?? [];
        $orderOptions = $_GET['orderOptions'] ?? [];
        if(isset($filters['state']) && StateJustif::tryFrom($filters['state']) !== null) {
            $filters['state'] = StateJustif::from($filters['state']);
        }
        if(isset($orderOptions['sortOrder']) && SortOrder::tryFrom($orderOptions['sortOrder']) !== null) {
            $orderOptions['sortOrder'] = SortOrder::from($orderOptions['sortOrder']);
        }

        try {
            $justifications = JustificationService::getJustificationsWithFilters($filters, $orderOptions);
            new ResponseApi(HttpStatus::OK, $justifications)->done();
        }
        catch (\InvalidArgumentException $e) {
            Notification::addNotification(NotificationType::Error, $e->getMessage());
            new ResponseApi(HttpStatus::BAD_REQUEST)->done();
        }
    }

    /**
     * Traité une justificatif
     *
     * @param array $params
     * @return void
     */
    public static function putDetailJustification(array $params): void {
        try {
            $justification = JustificationService::getJustificationById((int) $params['id']);
            $data = json_decode(file_get_contents('php://input'), true);

            $validator = new ProcessJustificationValidator($data);
            if(!$validator->checkAllGood()) {
                Notification::addNotification(NotificationType::Error, "Impossible de traiter votre demande, veuillez contacter l'administrateur");
                new ResponseApi(HttpStatus::BAD_REQUEST)->done();
            }
            $data = $validator->getData();

            JustificationService::processJustification($justification, $data);
            new ResponseApi(HttpStatus::NO_CONTENT)->done();
        }
        catch (EntityNotFoundException $e) {
            Notification::addNotification(NotificationType::Error, "Le justificatif demandé n'existe pas");
            new ResponseApi(HttpStatus::NOT_FOUND)->done();
        }
        catch (\BadMethodCallException $e) {
            Notification::addNotification(NotificationType::Error, "Le justificatif a déjà été traité");
            new ResponseApi(HttpStatus::BAD_REQUEST)->done();
        }
        catch (AbsenceNotProvidedException | CommentEducationalManagerNotProvidedException $e) {
            Notification::addNotification(NotificationType::Error, $e->getMessage());
            new ResponseApi(HttpStatus::BAD_REQUEST)->done();
        }
    }
}