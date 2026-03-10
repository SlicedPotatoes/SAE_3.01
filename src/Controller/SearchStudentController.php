<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Model\Entity\Account\GroupStudent;
use Uphf\GestionAbsence\Model\Validation\SearchStudentValidator;
use Uphf\GestionAbsence\Service\AccountService;
use Uphf\GestionAbsence\ViewModel\SearchStudentViewModel;

/**
 * Controller pour la recherche d'étudiant coté RP
 */
class SearchStudentController {
    /**
     * Page de recherche étudiant
     *
     * @return ControllerData
     */
    public static function showSearchStudent(): ControllerData {
        $filters = [
            'search' => null,
            'groupStudent' => null
        ];
        $students = AccountService::getFilteredStudents($filters);

        return new ControllerData(
            "/View/searchStudent.php",
            "Recherche étudiant",
            new SearchStudentViewModel(
                $students,
                GroupStudent::getAllGroupsStudent(),
                $filters
            )
        );
    }

    /**
     * Renvoi un JSON des étudiants trouvé avec les filtres, permet ainsi leurs affichages via Ajax JavaScript
     *
     * @return void (echo json)
     */
    public static function getSearchStudent(): void {
        $validator = new SearchStudentValidator();
        $filters = $validator->getData();

        $students = AccountService::getFilteredStudents($filters);
        echo json_encode($students);
    }
}