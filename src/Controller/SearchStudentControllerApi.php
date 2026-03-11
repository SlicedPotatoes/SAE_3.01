<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Model\Entity\Account\Student;
use Uphf\GestionAbsence\Model\Validation\SearchStudentValidator;
use Uphf\GestionAbsence\Service\AccountService;
use Uphf\GestionAbsence\Utils\ResponseApi\HttpStatus;
use Uphf\GestionAbsence\Utils\ResponseApi\ResponseApi;

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
        $validator = new SearchStudentValidator($_GET);
        $filters = $validator->getData();

        $students = AccountService::getFilteredStudents($filters);
        new ResponseApi(HttpStatus::OK, ["result" => $students])->done();
    }
}