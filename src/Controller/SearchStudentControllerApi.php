<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Model\Entity\Account\Student;
use Uphf\GestionAbsence\Model\Validation\SearchStudentValidator;
use Uphf\GestionAbsence\Service\AccountService;

/**
 * Controller Rest pour la recherche d'étudiant côté Responsable pédagogique
 */
class SearchStudentControllerApi
{
    /**
     * Renvoi un JSON des étudiants trouvé avec les filtres, permet ainsi leurs affichages via Ajax JavaScript
     *
     * @return void (echo json)
     */
    public static function getSearchStudent(): void {
        $validator = new SearchStudentValidator($_GET);
        $filters = $validator->getData();

        $students = AccountService::getFilteredStudents($filters);
        echo json_encode(Student::jsonSerializeStudent($students));
        exit();
    }
}