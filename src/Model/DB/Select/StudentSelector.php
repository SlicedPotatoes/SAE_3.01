<?php

namespace Uphf\GestionAbsence\Model\DB\Select;

use Uphf\GestionAbsence\Model\DB\Connection;
use Uphf\GestionAbsence\Model\Entity\Account\Student;
use Uphf\GestionAbsence\Model\Hydrator\AccountHydrator;
use PDO;

/**
 * Classe static, chaque méthode permet de récupérer des données en rapport avec un étudiant spécifique
 */
class StudentSelector {

    /**
     * Récupérer un étudiant à partir de son id
     *
     * @param int $idStudent
     * @return Student | null
     */
    public static function getStudentById(int $idStudent): Student | null {
        $res = TableSelector::fromTableWhere("studentAccount", ["studentid"], [[$idStudent, PDO::PARAM_INT]]);

        if(!empty($res)) {
            return AccountHydrator::unserializeStudent($res[0]);
        }

        return null;
    }

    /***
     * Récupérer le nombre d'absences total d'un étudiant
     *
     * @param int $idStudent
     * @return int
     */
    public static function getAbsTotal(int $idStudent): int {
        $connection = Connection::getInstance();
        $request = $connection->prepare("SELECT COUNT(*) FROM absence WHERE idStudent = ?");
        $request->bindParam(1, $idStudent);
        $request->execute();
        $result = $request->fetch();
        return $result[0];
    }

    /**
     * Récupérer le nombre d'absences pouvant être justifiées (allowedJustification = true)
     * @param int $idStudent
     * @return int
     */
    public static function getAbsCanBeJustified(int $idStudent): int {
        $connection = Connection::getInstance();

        $query = "SELECT COUNT(*) FROM absence WHERE idStudent = ? AND allowedJustification = true";

        $request = $connection->prepare($query);
        $request->bindParam(1, $idStudent);
        $request->execute();
        $result = $request->fetch();
        return $result[0];
    }

    /**
     * Récupérer le nombre de demi-journées d'absence (matin < 12h30 ; après-midi ≥ 12h30)
     *
     * Peut ainsi comptabiliser deux demi-journées d'absence le même jour.
     * @param int $idStudent
     * @return int
     */
    public static function getHalfdaysAbsences(int $idStudent): int {
        $connection = Connection::getInstance();

        $sql = "
        with view_morning_absences as (
            select a.idStudent, cast(time as date) as day
            from absence a
            where cast(a.time as time) < time '12:30' 
            group by a.idStudent, day
        ),
        view_afternoon_absences as (
            select a.idStudent, cast(time as date) as day
            from absence a
            where cast(a.time as time) >= time '12:30'
            group by idStudent, day
        ),
        view_halfdays_absence as (
            select idstudent, day from view_morning_absences
            union all 
            select idstudent, day from view_afternoon_absences
        )
        
        select count(*)
        from view_halfdays_absence
        where idstudent = :idstudent;";

        $query = $connection->prepare($sql);
        $query->bindValue(':idstudent', $idStudent, PDO::PARAM_INT);
        $query->execute();

        return (int)$query->fetchColumn();
    }

    /**
     * Récupérer le malus cosé par les demi-journées d'absence.
     *
     * Le malus est calculé sur les demi-journées ayant des absences avec les états suivants:
     * - Pending
     * - NotJustified
     * - Refused
     *
     * 0 si malus < seuil, sinon demiJournees * taux
     * @param int $idStudent
     * @return float
     */
    public static function getMalusPoints(int $idStudent): float {
        $connection = Connection::getInstance();

        $sql = "
        with view_morning_absences as 
        (
            select a.idStudent, cast(time as date) as day
            from absence a
            where cast(a.time as time) < time '12:30' 
                and currentState in ('Refused','NotJustified','Pending')
            group by a.idStudent, day
        ),
        view_afternoon_absences as 
        (
            select a.idStudent, cast(time as date) as day
            from absence a
            where cast(a.time as time) >= time '12:30' 
                and currentState in ('Refused','NotJustified','Pending')
            group by idStudent, day
        ),
        view_halfdays_absence as 
        (
            select idstudent, day from view_morning_absences
            union all 
            select idstudent, day from view_afternoon_absences
        )
        
        select count(*)
        from view_halfdays_absence
        where idstudent = :idstudent;";

        $query = $connection->prepare($sql);
        $query->bindValue(':idstudent', $idStudent, PDO::PARAM_INT);
        $query->execute();

        $halfdays = (int)$query->fetchColumn();

        return ($halfdays >= Student::MALUS_THRESHOLD) ? $halfdays * Student::MALUS_POINTS : 0.0;
    }

    /**
     * Récupérer le malus cosé par les demi-journées d'absence.
     *
     * Le malus est calculé sur les mêmes états que la méthode getMalusPoints()
     * en excluant l'état Pending.
     *
     * Utilisé pour afficher l'impacte de la validation des absences en attente
     *
     * @param int $idStudent
     * @return float
     */
    public static function getMalusPointsWithoutPending(int $idStudent): float {
        $connection = Connection::getInstance();

        $sql = "
        with view_morning_absences as 
        (
            select a.idStudent, cast(time as date) as day
            from absence a
            where cast(a.time as time) < time '12:30' 
                and currentState in ('Refused','NotJustified')
            group by a.idStudent, day
        ),
        view_afternoon_absences as 
        (
            select a.idStudent, cast(time as date) as day
            from absence a
            where cast(a.time as time) >= time '12:30' 
                and currentState in ('Refused','NotJustified')
            group by idStudent, day
        ),
        view_halfdays_absence as 
        (
            select idstudent, day from view_morning_absences
            union all 
            select idstudent, day from view_afternoon_absences
        )
        
        select count(*)
        from view_halfdays_absence
        where idstudent = :idstudent;
        ";

        $query = $connection->prepare($sql);
        $query->bindValue(':idstudent', $idStudent, PDO::PARAM_INT);
        $query->execute();

        $halfdays = (int)$query->fetchColumn();

        return ($halfdays >= Student::MALUS_THRESHOLD) ? $halfdays * Student::MALUS_POINTS : 0.0;
    }

    /**
     * Récupérer le nombre d'absences "Pénalisante"
     *
     * Cela inclut toutes les absences avec l'état suivant:
     * - Pending
     * - NotJustified
     * - Refused
     *
     * @param int $idStudent
     * @return int
     */
    public static function getPenalizingAbsence(int $idStudent): int {
        $connection = Connection::getInstance();
        $request = $connection->prepare("SELECT COUNT(*) FROM absence WHERE idStudent = ? and currentState in ('Refused','NotJustified', 'Pending')");
        $request->bindParam(1, $idStudent);
        $request->execute();
        $result = $request->fetch();
        return $result[0];
    }

    /**
     * Récupérer le nombre de demi-journée d'absences "Pénalisante"
     *
     * Cela inclut toutes les absences avec l'état suivant:
     * - Pending
     * - NotJustified
     * - Refused
     *
     * @param int $idStudent
     * @return int
     */
    public static function getHalfdayPenalizingAbsence(int $idStudent): int {
        $connection = Connection::getInstance();

        $sql = "
        WITH view_morning_absences AS (
            SELECT a.idStudent, CAST(a.time AS date) AS day
            FROM absence a
            WHERE CAST(a.time AS time) < TIME '12:30'
              AND currentState IN ('Refused', 'NotJustified', 'Pending')
            GROUP BY a.idStudent, day
        ),
        view_afternoon_absences AS (
            SELECT a.idStudent, CAST(a.time AS date) AS day
            FROM absence a
            WHERE CAST(a.time AS time) >= TIME '12:30'
              AND currentState IN ('Refused', 'NotJustified', 'Pending')
            GROUP BY a.idStudent, day
        ),
        view_halfdays_absence AS (
            SELECT idStudent, day FROM view_morning_absences
            UNION ALL
            SELECT idStudent, day FROM view_afternoon_absences
        )
        SELECT COUNT(*)
        FROM view_halfdays_absence
        WHERE idStudent = :idStudent; ";

        $query = $connection->prepare($sql);
        $query->bindValue(':idStudent', $idStudent, PDO::PARAM_INT);
        $query->execute();

        return (int)$query->fetchColumn();
    }
}
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . "/../../../../vendor/autoload.php";

$student = StudentSelector::getStudentById(1);
echo $student->getLastName() . " " . $student->getFirstName();
*/