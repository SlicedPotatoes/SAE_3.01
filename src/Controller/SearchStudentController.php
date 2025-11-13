<?php

namespace Uphf\GestionAbsence\Controller;

use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\DB\Select\SelectBuilder\StudentSelectBuilder;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\Model\Entity\Account\GroupStudent;
use Uphf\GestionAbsence\Model\CheckValidity;
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

        $filter = []; // Filtre appliqué a la currTab

        // Gestion des filtres, vérifier s'ils sont envoyé via POST, que les valeurs sont correctes et les appliqués au builder
        if($_SERVER['REQUEST_METHOD'] == "POST") {
            if(CheckValidity::haveValue("POST", "search")) {
                $filter['search'] = $_POST['search'];
                $builderStudents->searchBar($_POST['search']);
            }
            else {
                $builderStudents->allStudent();
            }

            if(CheckValidity::isValidInt("POST", "groupStudent")) {
                $filter['idGroup'] = $_POST['groupStudent'];
                $builderStudents->groupStudent((int)$_POST['groupStudent']);
            }
        }
        // Initialiser le builder sans filtre
        else {
            $builderStudents->allStudent();
        }

        $students = $builderStudents->execute();

        return new ControllerData(
            "/View/searchStudent.php",
            "Recherche étudiant",
            new SearchStudentViewModel(
                $students,
                GroupStudent::getAllGroupsStudent(),
                $filter
            )
        );
    }
}