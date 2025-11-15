<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\DB\Select\SelectBuilder\StudentSelectBuilder;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\Model\Entity\Account\GroupStudent;
use Uphf\GestionAbsence\Model\Validation\SearchStudentValidator;
use Uphf\GestionAbsence\ViewModel\SearchStudentViewModel;

/**
 * Controller pour la recherche d'étudiant coté RP
 */
class SearchStudentController {
    /**
     * Si l'utilisateur n'est pas connecté => Rediriger vers login
     *
     * Si l'utilisateur n'est pas RP => 403
     *
     * Gestion des filtres appliquée
     *
     * @return ControllerData
     */
    public static function show(): ControllerData {
        // Utilisateur non connecté, rediriger vers /
        if(!AuthManager::isLogin()) {
            header("Location: /");
            exit();
        }

        // Si compte autre que RP => 403
        if(!AuthManager::isRole(AccountType::EducationalManager)) {
            return ControllerData::get403();
        }

        // Builder pour récupérer les étudiants
        $builderStudents = new StudentSelectBuilder();

        $validator = new SearchStudentValidator();
        $filters = $validator->getData();

        // Gestions des filtres
        if(isset($filters['search'])) {
            $builderStudents->searchBar($filters['search']);
        }
        else {
            $builderStudents->allStudent();
        }
        if(isset($filters['groupStudent'])) {
            $builderStudents->groupStudent($filters['groupStudent']);
        }

        $students = $builderStudents->execute();

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
}