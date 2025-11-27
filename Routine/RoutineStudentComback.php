<?php
namespace Uphf\GestionAbsence\Routine;

use DateTime;
use PDO;
use Uphf\GestionAbsence\Model\DB\Connection;

class RoutineStudentComback {
    private static string $view = "
    WITH absToday AS (
        SELECT idstudent
        FROM absence
        WHERE CAST(:date AS timestamp) <= time AND CAST(:date AS timestamp) + '1 day' >= time
        GROUP BY idstudent 
    )
    ";

    /**
     * Incrémenté la column consecutiveAbsenceDays de student
     *
     * Si celui-ci est absent à la date passée en paramètre
     *
     * @param DateTime $date
     * @return void
     */
    private static function incrementConsecutiveDays(DateTime $date): void {
        $pdo = Connection::getInstance();

        $query = self::$view .
        "UPDATE Student
         SET consecutiveabsencedays = consecutiveabsencedays + 1
         WHERE idaccount in (
             SELECT * FROM absToday
         )";

        $sql = $pdo->prepare($query);
        $sql->bindValue(':date', $date->format('Y-m-d'));
        $sql->execute();
    }

    /**
     * Reset le compteur consecutiveAbsenceDays de student
     *
     * Mettre à jour de la column lastcomeback de student
     *
     * @param DateTime $date
     * @return void
     */
    private static function resetConsecutiveDays(DateTime $date): void {
        $pdo = Connection::getInstance();

        $query = self::$view .
        "UPDATE Student
         SET consecutiveabsencedays = 0,
             lastcomeback = :date
         WHERE idaccount not in (
             SELECT * FROM absToday
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

        $query = "SELECT idaccount, EXTRACT(DAY FROM (CAST(:date AS timestamp) - lastcomeback)) AS days
                  FROM student
                  WHERE CAST(:date AS timestamp) - lastcomeback <= INTERVAL '2 days'";

        $sql = $pdo->prepare($query);
        $sql->bindValue(':date', $date->format("Y-m-d"));
        $sql->execute();

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function executeRoutine(DateTime $date): void {
        $students = self::getStudentComeback($date);

    }
}
