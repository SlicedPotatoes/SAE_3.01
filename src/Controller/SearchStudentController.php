<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Database\Select\AccountSelector;
use Uphf\GestionAbsence\Database\Select\SelectBuilder\StudentSelectBuilder;
use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\Model\Entity\Account\GroupStudent;
use Uphf\GestionAbsence\Model\Validation\SearchStudentValidator;
use Uphf\GestionAbsence\Service\AccountService;
use Uphf\GestionAbsence\ViewModel\SearchStudentViewModel;

/**
 * Controller pour la recherche d'étudiant coté RP
 */
class SearchStudentController {
    /**
     * Si l'utilisateur n'est pas connecté => Rediriger vers login
     *
     * Page de recherche étudiant
     *
     * @return ControllerData
     */
    public static function showSearchStudent(): ControllerData {
        /**
         * TODO : A RETIRER QUAND LES ROUTES SERONT REFAIT
         *
         * Si n'est pas RP ou Secrétaire redirection vers la page 403
         */
        if(!AuthManager::isRole(AccountType::EducationalManager)) {
            return ControllerData::get403();
        }

        $filters = [ 'search' => null,
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

        $students = AccountSelector::getStudentsByFilters($filters);
        echo json_encode($students);
    }
}