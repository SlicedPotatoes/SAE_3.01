<?php

namespace Uphf\GestionAbsence\Controller;

use Respect\Validation\Exceptions\NestedValidationException;
use Uphf\GestionAbsence\Model\Entity\Account\Student;
use Uphf\GestionAbsence\Model\Notification\Notification;
use Uphf\GestionAbsence\Model\Notification\NotificationType;
use Uphf\GestionAbsence\Service\AccountService;
use Uphf\GestionAbsence\Utils\ResponseApi\HttpStatus;
use Uphf\GestionAbsence\Utils\ResponseApi\ResponseApi;
use Uphf\GestionAbsence\Validator\SearchStudentValidator;

/**
 * Controller Rest pour la recherche d'étudiant côté Responsable pédagogique
 */
class SearchStudentControllerApi
{
    /**
     * GET /api/students
     * Renvoi un JSON des étudiants trouvé avec les filtres, permet ainsi leurs affichages via Ajax JavaScript
     */
    public static function getSearchStudent(): void {
        try {
            SearchStudentValidator::validationFilterGetStudents($_GET);
            $students = AccountService::getFilteredStudents($_GET);

            new ResponseApi(HttpStatus::OK, $students)->done();
        }
        catch (NestedValidationException $e) {
            foreach ($e->getMessages() as $message) {
                Notification::addNotification(NotificationType::Error, $message);
            }

            new ResponseApi(HttpStatus::BAD_REQUEST)->done();
        }
    }
}