<?php
namespace Uphf\GestionAbsence\Routine;

use DateTime;
use PDO;
use Uphf\GestionAbsence\Model\DB\Connection;
use Uphf\GestionAbsence\Model\Mailer;

/**
 * Classe routine, permet la détection de retour d'étudiant apres une période d'absence, et envoie de mail a l'étudiant
 */
class RoutineStudentComback {
    // Vue pour récupérer les id des étudiants absents a la date fournie
    private static string $studentAbsTodayView = "
    SELECT idstudent
    FROM absence
    WHERE time >= CAST(:date AS timestamp) AND time < CAST(:date AS timestamp) + '1 day'
    GROUP BY idstudent 
    ";

    // Vue pour récupérer les id des étudiants ayant une période d'abs en cour
    private static string $studentHavePeriodView = "
    SELECT idstudent
    FROM studentperiodabs
    WHERE enddate IS NULL
    ";


    /**
     * Si un étudiant est absent à la date \$date
     *
     * Et qu'il n'a pas de période d'absence en cour
     *
     * Alors créer une nouvelle période d'absence pour cet étudiant
     *
     * @param DateTime $date
     * @return void
     */
    private static function newStudentPeriodAbs(DateTime $date): void {
        $pdo = Connection::getInstance();

        $query = "WITH studentAbsToday AS ("
                    . self::$studentAbsTodayView .
                 "),
                  studentHavePeriod AS ("
                    . self::$studentHavePeriodView .
                  ")".
                  "INSERT INTO studentperiodabs(idstudent, startdate, consecutivedays)
                   SELECT studentid, :date, 1
                   FROM (
                        SELECT * FROM studentAbsToday
                        EXCEPT
                        SELECT * FROM studentHavePeriod
                   ) AS result(studentid)";

        $sql = $pdo->prepare($query);
        $sql->bindValue(':date', $date->format('Y-m-d'));
        $sql->execute();
    }

    /**
     * Si un étudiant est absent à la date \$date
     *
     * Et que l'étudiant a une période d'absence
     *
     * Alors, incrémente le nombre de jours consécutif d'abs
     *
     * @param DateTime $date
     * @return void
     */
    private static function incrementConsecutiveDays(DateTime $date): void {
        $pdo = Connection::getInstance();

        $query = "WITH studentAbsToday AS ("
                    . self::$studentAbsTodayView .
                 "),
                  studentHavePeriod AS ("
                    . self::$studentHavePeriodView .
                 ")".
                 "UPDATE studentperiodabs
                  SET consecutivedays = consecutivedays + 1
                  WHERE idstudent in (
                      SELECT * FROM studentAbsToday
                      INTERSECT 
                      SELECT * FROM studentHavePeriod
                  )";

        $sql = $pdo->prepare($query);
        $sql->bindValue(':date', $date->format('Y-m-d'));
        $sql->execute();
    }

    /**
     * Met fin à une période d'absence pour un étudiant qui est revenu
     *
     * @param DateTime $date
     * @return void
     */
    private static function resetConsecutiveDays(DateTime $date): void {
        $pdo = Connection::getInstance();

        $query = "WITH studentAbsToday AS ("
                    . self::$studentAbsTodayView .
                 "),
                  studentHavePeriod AS ("
                    . self::$studentHavePeriodView .
                 ")".
                 "UPDATE studentperiodabs
                  SET enddate = CAST(:date AS timestamp)
                  WHERE idstudent in (
                      SELECT * FROM studentHavePeriod
                      EXCEPT 
                      SELECT * FROM studentAbsToday
                  )";

        $sql = $pdo->prepare($query);
        $sql->bindValue(':date', $date->format('Y-m-d'));
        $sql->execute();
    }

    /**
     * Renvoie la liste des étudiants revenus il y a moins de 2j
     *
     * @param DateTime $date
     * @return array
     */
    private static function getStudentComeback(DateTime $date): array {
        $pdo = Connection::getInstance();

        $query = "WITH period AS (
                    SELECT *
                    FROM studentperiodabs
                    WHERE enddate IS NOT NULL AND CAST(:date AS timestamp) - enddate <= INTERVAL '2 days'
                ),
                haveExam AS (
                    SELECT a.idstudent, count(*) >= 1 AS haveExam
                    FROM absence a
                    JOIN period p ON a.idstudent = p.idstudent
                    WHERE a.time >= p.startdate AND
                          a.time < p.enddate + INTERVAL '1 day' AND
                          a.examen
                    GROUP BY a.idstudent
                ),
                view_morning_absences as (
                    select a.idStudent, cast(time as date) as day
                    from absence a
                    where cast(a.time as time) < time '12:30'
                        and currentState in ('Refused','NotJustified','Pending')
                    group by a.idStudent, day
                ),
                view_afternoon_absences as (
                    select a.idStudent, cast(time as date) as day
                    from absence a
                    where cast(a.time as time) >= time '12:30'
                        and currentState in ('Refused','NotJustified','Pending')
                    group by idStudent, day
                ),
                view_halfdays_absence as (
                    select idstudent, day from view_morning_absences
                    union all
                    select idstudent, day from view_afternoon_absences
                ),
                demiDaysAbs as (
                    select idstudent, count(*) as demiDaysAbs
                    from view_halfdays_absence
                    group by idstudent
                ),
                view_morning_absences_period as (
                    select a.idStudent, cast(time as date) as day
                    from absence a
                    JOIN period p ON a.idstudent = p.idstudent
                    where cast(a.time as time) < time '12:30' and
                          currentState in ('Refused','NotJustified','Pending') AND
                          a.time >= p.startdate AND
                          a.time < p.enddate + INTERVAL '1 day'
                    group by a.idStudent, day
                ),
                view_afternoon_absences_period as (
                    select a.idStudent, cast(time as date) as day
                    from absence a
                    JOIN period p ON a.idstudent = p.idstudent
                    where cast(a.time as time) >= time '12:30' and
                          currentState in ('Refused','NotJustified','Pending') AND
                          a.time >= p.startdate AND
                          a.time < p.enddate + INTERVAL '1 day'
                    group by a.idStudent, day
                ),
                view_halfdays_absence_period as (
                    select idstudent, day from view_morning_absences_period
                    union all
                    select idstudent, day from view_afternoon_absences_period
                ),
                demiDaysAbs_period as (
                    select idstudent, count(*) as demiDaysAbs
                    from view_halfdays_absence_period
                    group by idstudent
                )
                SELECT
                    a.lastname,
                    a.firstname,
                    a.email,
                    p.startdate,
                    p.enddate,
                    COALESCE(he.haveExam, false) AS haveExam,
                    CASE
                        WHEN dda.demiDaysAbs >= 5 THEN dda.demiDaysAbs * 0.1
                        ELSE 0
                    END AS currMalus,
                    CASE
                        WHEN (dda.demiDaysAbs - ddap.demiDaysAbs) >= 5 THEN (dda.demiDaysAbs - ddap.demiDaysAbs) * 0.1
                        ELSE 0
                    END AS malusPrevision,
                    EXTRACT(DAY FROM (:date - p.enddate)) AS days
                
                FROM Account a
                JOIN period p ON a.idaccount = p.idstudent
                LEFT JOIN haveExam he ON a.idaccount = he.idstudent
                JOIN demiDaysAbs dda ON a.idaccount = dda.idstudent
                JOIN demiDaysAbs_period ddap ON a.idaccount = ddap.idstudent";

        $sql = $pdo->prepare($query);
        $sql->bindValue(':date', $date->format("Y-m-d"));
        $sql->execute();

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Détection de retour + envoie du mail
     *
     * @param DateTime $date
     * @return void
     */
    public static function executeRoutine(DateTime $date): void {
        self::incrementConsecutiveDays($date);
        self::newStudentPeriodAbs($date);
        self::resetConsecutiveDays($date);

        $students = self::getStudentComeback($date);

        foreach($students as $student) {
            if($student['days'] == 2) { continue; }

            echo "<pre>";
            var_export($student);
            echo "</pre>";

            Mailer::sendReturnAlert(
                $student['lastname'],
                $student['firstname'],
                $student['email'],
                DateTime::createFromFormat("Y-m-d h:i:s", $student['startdate']),
                DateTime::createFromFormat("Y-m-d h:i:s", $student['enddate']),
                $student['haveexam'] === true,
                $student['days'] == 1,
                $student['currmalus'],
                $student['malusprevision']
            );
        }
    }
}
