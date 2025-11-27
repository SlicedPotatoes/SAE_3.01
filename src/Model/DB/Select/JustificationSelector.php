<?php

namespace Uphf\GestionAbsence\Model\DB\Select;

use Uphf\GestionAbsence\Model\DB\Connection;
use Uphf\GestionAbsence\Model\Entity\Absence\Absence;
use Uphf\GestionAbsence\Model\Entity\Justification\File;
use Uphf\GestionAbsence\Model\Entity\Justification\Justification;
use Uphf\GestionAbsence\Model\Hydrator\AbsenceHydrator;
use Uphf\GestionAbsence\Model\Hydrator\JustificationHydrator;
use PDO;

/**
 * Classe static, chaque méthode permet de récupérer des données en rapport avec un justificatif spécifique
 */
class JustificationSelector {

    /**
     * Récupérer un justificatif à partir de son id
     *
     * @param int $idJustification
     * @return Justification | null
     */
    public static function getJustificationById(int $idJustification): Justification | null {
        $conn = Connection::getInstance();

        $query = "SELECT DISTINCT j.*, s.*
                  FROM justification j
                  JOIN absenceJustification aj USING(idJustification)
                  JOIN studentAccount s ON s.studentid = aj.idstudent
                  WHERE idJustification = :idJustification";

        $sql = $conn->prepare($query);
        $sql->bindValue(":idJustification", $idJustification, PDO::PARAM_INT);
        $sql->execute();;

        $res = $sql->fetch(PDO::FETCH_ASSOC);

        if($res) {
            return JustificationHydrator::unserializeJustification($res);
        }

        return null;
    }

    /**
     * Récupérer les fichiers associés à un justificatif
     *
     * @param int $idJustification
     * @return File[]
     */
    public static function getFiles(int $idJustification): array {
        $rows = TableSelector::fromTableWhere("file", ["idJustification"], [[$idJustification, PDO::PARAM_INT]]);

        $files = [];

        foreach($rows as $row) {
            $files[] = JustificationHydrator::unserializeFile($row);
        }

        return $files;
    }

    /**
     * Récupérer les absences associées à un justificatif
     *
     * @param int $idJustification
     * @return Absence[]
     */
    public static function getAbsences(int $idJustification): array {
        $conn = Connection::getInstance();

        $query = "SELECT
                    s.studentid,
                    s.lastname AS studentLastName,
                    s.firstname AS studentFirstName,
                    s.email AS studentEmail,
                    s.accounttype AS studentAccountType,
                    s.studentnumber,
                    s.groupid,
                    s.grouplabel,
                    a.*,
                    r.*,
                    t.idaccount AS teacherid,
                    t.lastname AS teacherLastName,
                    t.firstname AS teacherFirstName,
                    t.email AS teacherEmail,
                    t.accounttype AS teacherAccountType 
                  FROM AbsenceJustification
                  JOIN Absence a USING(idStudent, time)
                  JOIN Resource r USING(idResource)
                  LEFT JOIN Account t ON a.idTeacher = t.idAccount
                  JOIN StudentAccount s ON a.idStudent = s.studentid
                  WHERE idJustification = :idJustification
                  ORDER BY time ASC";

        $sql = $conn->prepare($query);
        $sql->bindValue(":idJustification", $idJustification, PDO::PARAM_INT);
        $sql->execute();

        $rows = $sql->fetchAll(PDO::FETCH_ASSOC);
        $absences = [];

        foreach($rows as $row) {
            $absences[] = AbsenceHydrator::unserializeAbsence($row);
        }

        return $absences;
    }
}
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . "/../../../../vendor/autoload.php";

$j = JustificationSelector::getJustificationById(86);
echo $j->getIdJustification();

$files = JustificationSelector::getFiles(86);
foreach ($files as $file) {
    echo "<pre>" . $file->getFileName() . "</pre>";
}

$absences = JustificationSelector::getAbsences(86);
echo count($absences);
*/