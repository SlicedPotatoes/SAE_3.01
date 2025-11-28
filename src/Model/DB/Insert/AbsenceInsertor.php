<?php

namespace Uphf\GestionAbsence\Model\DB\Insert;

use DateTime;
use PDO;
use Uphf\GestionAbsence\Model\DB\Connection;

/**
 * Classe gérent l'insertion d'absence dans la BDD depuis un export VT
 */
class AbsenceInsertor {
    /**
     * Insère les absences dans la bdd
     *
     * Renvoie le nombre d'absences total et le nombre d'absences sans doublon
     *
     * @param array $list
     * @return int[]
     */
    public static function addAbsences(array $list): array {
        self::deleteLateArrival($list);
        $nbAbs = count($list);

        if($nbAbs === 0) { return [0, 0]; }

        self::checkDuplication($list);
        $nbAbsWithoutDuplication = count($list);
        if($nbAbsWithoutDuplication === 0) { return [$nbAbs, 0]; }

        $pdo = Connection::getInstance();

        $values = [];
        $params = [];

        // Préparation pour construire la requête
        foreach($list as $i => $abs) {
            $studentNumber = $abs['Identifiant'];
            $time = self::getTime($abs['Date'], $abs['Heure']);
            $duration = self::getInterval($abs['Durée']);
            $typeCourse = $abs['Type'];
            $ressourceLabel = $abs['Matière'];
            $groupe = $abs['Groupes'];
            $prof = $abs['Profs'];
            $exam = $abs['Contrôle'] === "Oui";

            $values[] = "(CAST(:studentNumber$i AS INT), cast(:time$i AS timestamp), CAST(:duration$i AS interval), :courseType$i, :ressource$i, :groups$i, :prof$i, CAST(:exam$i AS BOOLEAN))";
            $params[":studentNumber$i"] = [$studentNumber, PDO::PARAM_INT];
            $params[":time$i"] = [$time, PDO::PARAM_STR];
            $params[":duration$i"] = [$duration, PDO::PARAM_STR];
            $params[":courseType$i"] = [$typeCourse->value, PDO::PARAM_STR];
            $params[":ressource$i"] = [$ressourceLabel, PDO::PARAM_STR];
            $params[":groups$i"] = [$groupe, PDO::PARAM_STR];
            $params[":prof$i"] = [$prof, PDO::PARAM_STR];
            $params[":exam$i"] = [$exam, PDO::PARAM_BOOL];
        }

        // Construction de la requête
        $query = "INSERT INTO Absence
                  SELECT v.time,
                         v.duration,
                         v.exam,
                         true,
                         ta.idAccount,
                         sa.idAccount,
                         'NotJustified',
                         v.courseType::coursetype,
                         r.idResource,
                         null,
                         v.groupLabel
                  FROM
                  (
                      (
                        VALUES " . implode(", ", $values) . "
                      ) AS v(studentNumber, time, duration, courseType, ressourceLabel, groupLabel, profFullName, exam)
                      JOIN Student s ON v.studentNumber = s.studentNumber
                      JOIN Account sa ON s.idAccount = sa.idAccount
                      LEFT JOIN Account ta ON ta.lastname || ' ' || ta.firstname = v.profFullName
                      JOIN resource r ON r.label = v.ressourceLabel
                  )";

        $sql = $pdo->prepare($query);
        // Bind des paramètres
        foreach($params as $key => [$value, $type]) {
            $sql->bindValue($key, $value, $type);
        }
        $sql->execute();

        return [$nbAbs, $nbAbsWithoutDuplication];
    }

    /**
     * Supprime les lignes qui ne sont pas "Absence"
     *
     * @param $list
     * @return void
     */
    public static function deleteLateArrival(&$list): void {
        foreach($list as $i => $abs) {
            if($abs['Absent/Présent'] != 'Absence') {
                unset($list[$i]);
            }
        }
    }

    /**
     * Supprime les doublons de la liste passée en paramètre, en vérifiant l'existence de l'absence dans la BDD
     *
     * La liste passée en paramètre est une référence
     *
     * @param array $list
     * @return void
     */
    private static function checkDuplication(array &$list): void {
        if(count($list) === 0) { return; }
        $pdo = Connection::getInstance();

        $query = "SELECT * FROM absence JOIN Student ON Absence.idStudent = Student.idAccount";

        $where = [];
        $params = [];

        // Construction de la requête pour trouver les doublons
        foreach($list as $i => $abs) {
            $where[] = "(time = :time$i AND studentNumber = :studentNumber$i)";

            $hourRaw = explode("H", $abs['Heure']);
            $dateRaw = $abs['Date'] . " " . $hourRaw[0] . ':' .$hourRaw[1];
            $params["time$i"] = DateTime::createFromFormat("d/m/Y H:i", $dateRaw)->format("Y-m-d H:i:s.u");
            $params["studentNumber$i"] = $abs['Identifiant'];
        }

        $query .= " WHERE " . implode(' OR ', $where);

        // Bind des paramètres
        $sql = $pdo->prepare($query);
        foreach($params as $key => $value) {
            $sql->bindValue(":$key", $value);
        }

        $sql->execute();
        $res = $sql->fetchAll(PDO::FETCH_ASSOC);

        // Création d'un "set" (un peu improvisé), pour optimiser la complexité
        $toDelete = [];
        foreach($res as $d) {
            $toDelete[$d['time'].';'.$d['studentnumber']] = null;
        }

        // Suppression des doublons
        foreach($list as $key => $abs) {
            $dateFormated = self::getTime($abs['Date'], $abs['Heure']);
            $keyInSet = $dateFormated.';'.$abs['Identifiant'];

            if(array_key_exists($keyInSet, $toDelete)) {
                unset($list[$key]);
            }
        }
    }

    /**
     * Formate un string au format "XXHXX" au format "XX:XX"
     *
     * @param string $hour
     * @return string
     */
    private static function getInterval(string $hour): string {
        [$h, $m] = explode('H', $hour);
        return "$h:$m";
    }

    /**
     * Prend \$date au format "d/m/Y" et \$hour au format "XXHXX"
     *
     * Renvoie une chaine représentant une date pour la BDD au format "Y-m-d H:i:s"
     *
     * @param string $date
     * @param string $hour
     * @return string
     */
    private static function getTime(string $date, string $hour):string {
        $interval = self::getInterval($hour);
        $dateRaw = "$date $interval";
        return DateTime::createFromFormat("d/m/Y H:i", $dateRaw)->format("Y-m-d H:i:s");
    }
}