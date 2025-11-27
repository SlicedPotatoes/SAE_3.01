<?php

namespace Uphf\GestionAbsence\Routine;
use PDO;
use Uphf\GestionAbsence\Model\DB\Connection;
use Uphf\GestionAbsence\Model\Mailer;

/**
 * Class routine, permet la détection d'absence longue et l'envoie de mail au RP.
 */
class RoutineLoungAbsencesMail
{
    /**
     * Récupérer les étudiants ayant une longue absence (5j consécutifs)
     *
     * @return array
     */
    private static function getStudentLongAbsence(): array
    {
        $pdo = Connection::getInstance();

        $query = "SELECT sa.lastname, sa.firstname, sa.studentnumber
          FROM studentaccount sa
          JOIN studentperiodabs spa ON sa.studentid = spa.idstudent
          WHERE spa.consecutivedays = 5 AND spa.enddate IS NULL";

        $sql = $pdo->prepare($query);
        $sql->execute();

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer les emails des RP
     *
     * @return array
     */
    private static function getEducationManagerMails(): array
    {
        $pdo = Connection::getInstance();

        $query = "SELECT email, lastname, firstname
              FROM Account
              WHERE accounttype = 'EducationalManager'";

        $sql = $pdo->prepare($query);
        $sql->execute();

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Exécuté la routine (Envoie de mails aux RP pour chaque étudiant avec 5 abs consécutifs)
     *
     * @return void
     */
    public static function executeRoutine(): void
    {
        $studentsLongAbsence = self::getStudentLongAbsence();
        $educationManagers = self::getEducationManagerMails();

        // Envoie des mails
        foreach ($studentsLongAbsence as $stu) {
            foreach ($educationManagers as $em) {
                Mailer::sendLongAbsenceAlert(
                    $em['lastname'],
                    $em['firstname'],
                    $em['email'],
                    $stu['lastname'],
                    $stu['firstname'],
                    $stu['studentnumber'],
                    5
                );
            }
        }
    }
}