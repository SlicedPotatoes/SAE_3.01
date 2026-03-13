<?php

namespace Uphf\GestionAbsence\Controller;

use DateTime;
use Exception;
use InvalidArgumentException;
use Respect\Validation\Exceptions\NestedValidationException;
use Uphf\GestionAbsence\Database\Insert\JustificationInsertor;
use Uphf\GestionAbsence\Exception\AbsenceNotProvidedException;
use Uphf\GestionAbsence\Exception\CommentEducationalManagerNotProvidedException;
use Uphf\GestionAbsence\Exception\EntityNotFoundException;
use Uphf\GestionAbsence\Model\Entity\Account\Student;
use Uphf\GestionAbsence\Model\FileUpload;
use Uphf\GestionAbsence\Model\Mailer;
use Uphf\GestionAbsence\Model\Notification\Notification;
use Uphf\GestionAbsence\Model\Notification\NotificationType;
use Uphf\GestionAbsence\Model\Validation\CreateJustificationValidator;
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
        }
        catch (EntityNotFoundException $e) {
            Notification::addNotification(NotificationType::Error, "Le justificatif demandé n'existe pas");
            new ResponseApi(HttpStatus::NOT_FOUND)->done();
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
            //Notification::addNotification(NotificationType::Error, "Impossible de traiter votre demande, veuillez contacter l'administrateur");
        }

        new ResponseApi(HttpStatus::BAD_REQUEST)->done();
    }


    private static function postJustification(Student $student): void {
        // L'utilisateur tente de créer un justificatif
        if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['action']) && $_POST['action'] == 'createJustification') {
            $validator = new CreateJustificationValidator();
            $errors = $validator->checkAllGood();

            if(!empty($errors)) {
                foreach($errors as $e) {
                    Notification::addNotification(NotificationType::Error, $e);
                }
                return;
            }

            $data = $validator->getData();
            $files = FileUpload::upload('files');

            try {
                // Créer le justificatif dans la BDD
                JustificationInsertor::insert(
                    $student->getIdAccount(),
                    $data['absenceReason'],
                    $data['startDate'],
                    $data['endDate'],
                    $files
                );

                // Envoyer le mail d'accusé de réception
                Mailer::sendAccRecpJustification(
                    $student->getLastName(),
                    $student->getFirstName(),
                    $student->getEmail(),
                    DateTime::createFromFormat("Y-m-d", $data['startDate'])->format("d/m/Y"),
                    DateTime::createFromFormat("Y-m-d", $data['endDate'])->format("d/m/Y")
                );

                Notification::addNotification(NotificationType::Success, "Justificatif envoyé avec succès");

                return;
            }
                // Exception levée par JustificationInsertor::insert, quand il n'y a pas d'absence justifiable sur la période sélectionnée
            catch (InvalidArgumentException $e) {
                Notification::reset();
                Notification::addNotification(NotificationType::Error, "Créer Justification: Il n'y a pas d'absence justifiable sur la période sélectionnée");
            }
            catch (Exception $e) {
                Notification::reset();
                Notification::addNotification(NotificationType::Error, "Créer Justification: Une erreur interne est survenue lors de l'upload. Veuillez réessayer plus tard.");
                error_log("Créer Justification: " . $e->getMessage());
            }

            // S'il y a eu une erreur critique pendent la création du justificatif, supprimer les fichiers du dossier upload
            FileUpload::deleteFiles($files);
        }
    }

}