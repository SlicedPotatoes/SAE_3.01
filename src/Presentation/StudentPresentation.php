<?php

require_once __DIR__ . "/../Model/Account/Student.php";
require_once __DIR__ . "/../Model/Account/GroupStudent.php";

/**
 * Classe StudentPresentation, permettant de faire la liaison entre Model et View pour tout ce qui concerne les etudiants
 */
class StudentPresentation
{
    /**
     * Récupération un etudiant, en fonction du contexte dans lequel il est demandé
     * @return Student
     */
    public static function getStudentAccountDashboard() : Student
    {
        if ($_SESSION["role"] == AccountType::Student)
        {
            return $_SESSION["account"];
        }
        // TODO: Faire que le else vérifie que le compte est RP
        else
        {
            if (isset($_GET['studentAccount']))
            {
                return Student::getStudentByIdAccount($_GET['studentAccount']);
            }
            else
            {
                header("location: index.php?currPage=404");
                exit();
            }
        }

        // TODO: Rediriger vers une page 403
    }

    /**
     * Récupérer la liste des étudiants
     *
     * S'assure que l'utilisateur a l'autorisation d'effectuer l'action
     *
     * @return Student[]
     */
    public static function getAllStudents($filter) : array {
        // TODO: Vérifier que le compte est RP

        return Student::getAllStudents($filter);

        // TODO: Rediriger vers une page 403
    }

    /**
     * Récupérer la liste des groupes d'étudiant
     *
     * S'assure que l'utilisateur a l'autorisation d'effectuer l'action
     *
     * @return GroupStudent[]
     */
    public static function getAllGroupsStudent(): array {
        // TODO: Vérifier que le compte est RP

        return GroupStudent::getAllGroupsStudent();

        // TODO: Rediriger vers une page 403
    }
}
