<?php
namespace Uphf\GestionAbsence\Presentation;

use Uphf\GestionAbsence\Model\Account\Student;
use Uphf\GestionAbsence\Model\Account\GroupStudent;
use Uphf\GestionAbsence\Model\Account\AccountType;

/**
 * Classe StudentPresentation, permettant de faire la liaison entre Model et View pour tout ce qui concerne les etudiants
 */
class StudentPresentation
{
    /**
     * Récupération un etudiant, en fonction du contexte dans lequel il est demandé
     *
     * - Si l'utilisateur connecté est un étudiant : est retourné son compte sauvegardé en $_SESSION
     * - Si l'utilisateur connecté est un responsable pédagogique : est retourné le compte étudiant qui est sauvegardé en $_GET
     *
     * @return Student
     */
    public static function getStudentAccount() : Student
    {
        if ($_SESSION["role"] == AccountType::Student)
        {
            return $_SESSION["account"];
        }
        else if ($_SESSION["role"] == AccountType::EducationalManager)
        {
            if (isset($_GET['studentId']) && is_numeric($_GET['studentId']))
            {
                $studentID = Student::getStudentByIdAccount($_GET['studentId']);
                if ($studentID !== null)
                {
                    return $studentID;
                }
            }
            header("location: index.php?currPage=404");
            exit();
        }
        else
        {
            // TODO: Rediriger vers une page 403 et pas 404
            header("location: index.php?currPage=404");
        }
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
