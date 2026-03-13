<?php

namespace Uphf\GestionAbsence\Controller;

use DateTime;
use Exception;
use InvalidArgumentException;
use Respect\Validation\Exceptions\NestedValidationException;
use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\Model\FileUpload;
use Uphf\GestionAbsence\Model\Notification\Notification;
use Uphf\GestionAbsence\Model\Notification\NotificationType;
use Uphf\GestionAbsence\Service\AbsenceService;
use Uphf\GestionAbsence\Service\JustificationService;
use Uphf\GestionAbsence\Service\MailService;
use Uphf\GestionAbsence\Utils\ResponseApi\HttpStatus;
use Uphf\GestionAbsence\Utils\ResponseApi\ResponseApi;
use Uphf\GestionAbsence\Validator\StudentProfileValidator;

/**
 * Controller api pour le profil étudiant
 */
class StudentProfileControllerApi
{
    /**
     * GET /api/absences
     * Récupérer les absences d'un étudiant avec des filtres
     */
    public static function getAbsences(): void
    {
        try {
            StudentProfileValidator::validationGetAbsences($_GET);

            if (AuthManager::isRole(AccountType::EducationalManager) && isset($_GET['idStudent'])) {
                $idStudent = (int) $_GET['idStudent'];
            }
            elseif (AuthManager::isRole(AccountType::Student)) {
                $idStudent = AuthManager::getAccount()->getIdAccount();
            }
            else {
                new ResponseApi(HttpStatus::FORBIDDEN)->done(); // Ne devrais pas arriver
            }

            $absences = AbsenceService::absenceSelectService($idStudent, $_GET);

            new ResponseApi(HttpStatus::OK, $absences)->done();
        }
        catch (NestedValidationException $e) {
            foreach ($e->getMessages() as $message) {
                Notification::addNotification(NotificationType::Error, $message);
            }
        }
        catch (Exception $e) {
            Notification::addNotification(NotificationType::Error, $e->getMessage());
        }
        new ResponseApi(HttpStatus::BAD_REQUEST)->done();
    }

    /**
     * POST /api/justifications
     * Permet d'ajouter un nouveau justificatif pour l'étudiant connecté
     */
    public static function postJustification(): void {
        try {
            StudentProfileValidator::validationPostJustification($_POST);
            $files = FileUpload::upload('files');

            JustificationService::addJustification(
                AuthManager::getAccount()->getIdAccount(),
                $_POST,
                $files
            );

            MailService::sendAccRecepJustification(
                AuthManager::getAccount(),
                DateTime::createFromFormat("Y-m-d", $_POST['startDate']),
                DateTime::createFromFormat("Y-m-d", $_POST['endDate'])
            );

            Notification::addNotification(NotificationType::Success, "Justificatif envoyé avec succès");
            new ResponseApi(HttpStatus::NO_CONTENT)->done();
        }
        catch (NestedValidationException $e) {
            Notification::reset();
            foreach ($e->getMessages() as $message) {
                Notification::addNotification(NotificationType::Error, $message);
            }
        }
        catch (InvalidArgumentException $e) {
            Notification::reset();
            Notification::addNotification(NotificationType::Error, "Il n'y a pas d'absence justifiable sur la période sélectionnée");
        }
        catch (\Exception $e) {
            Notification::addNotification(NotificationType::Error, $e->getMessage());
        }

        new ResponseApi(HttpStatus::BAD_REQUEST)->done();
    }
}