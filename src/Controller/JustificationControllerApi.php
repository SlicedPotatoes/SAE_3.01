<?php

namespace Uphf\GestionAbsence\Controller;

use Respect\Validation\Exceptions\NestedValidationException;
use Uphf\GestionAbsence\Exception\AbsenceNotProvidedException;
use Uphf\GestionAbsence\Exception\CommentEducationalManagerNotProvidedException;
use Uphf\GestionAbsence\Exception\EntityNotFoundException;
use Uphf\GestionAbsence\Model\Notification\Notification;
use Uphf\GestionAbsence\Model\Notification\NotificationType;
use Uphf\GestionAbsence\Service\JustificationService;
use Uphf\GestionAbsence\Utils\ResponseApi\HttpStatus;
use Uphf\GestionAbsence\Utils\ResponseApi\ResponseApi;
use Uphf\GestionAbsence\Validator\JustificationValidator;

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
        try {
            JustificationValidator::validationGetJustificationList($_GET);

            $filters = $_GET['filters'] ?? [];
            $orderOptions = $_GET['orderOptions'] ?? [];

            $justifications = JustificationService::getJustificationsWithFilters($filters, $orderOptions);
            new ResponseApi(HttpStatus::OK, $justifications)->done();
            return;
        }
        catch (NestedValidationException $e) {
            foreach ($e->getMessages() as $message) {
                Notification::addNotification(NotificationType::Error, $message);
            }
        }
        catch (\InvalidArgumentException $e) {
            Notification::addNotification(NotificationType::Error, $e->getMessage());
        }
        new ResponseApi(HttpStatus::BAD_REQUEST)->done();
    }

    /**
     * Traité un justificatif
     *
     * @param array $params
     * @return void
     */
    public static function putDetailJustification(array $params): void {
        try {
            $justification = JustificationService::getJustificationById((int) $params['id']);
            $data = json_decode(file_get_contents('php://input'), true);

            JustificationValidator::validationPutDetailJustification($data);
            JustificationService::processJustification($justification, $data);

            new ResponseApi(HttpStatus::NO_CONTENT)->done();
            return;
        }
        catch (EntityNotFoundException $e) {
            Notification::addNotification(NotificationType::Error, "Le justificatif demandé n'existe pas");
            new ResponseApi(HttpStatus::NOT_FOUND)->done();
            return;
        }
        catch (\BadMethodCallException $e) {
            Notification::addNotification(NotificationType::Error, "Le justificatif a déjà été traité");
        }
        catch (AbsenceNotProvidedException | CommentEducationalManagerNotProvidedException $e) {
            Notification::addNotification(NotificationType::Error, $e->getMessage());
        }
        catch (NestedValidationException $e) {
            foreach ($e->getMessages() as $message) {
                Notification::addNotification(NotificationType::Error, $message);
            }
        }

        new ResponseApi(HttpStatus::BAD_REQUEST)->done();
    }
}