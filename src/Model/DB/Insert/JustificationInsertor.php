<?php

namespace Uphf\GestionAbsence\Model\DB\Insert;

use Uphf\GestionAbsence\Model\DB\Connection;
use Uphf\GestionAbsence\Model\DB\Select\SelectBuilder\AbsenceSelectBuilder;
use Uphf\GestionAbsence\Model\DB\Update\UpdateBuilder\AbsenceUpdateBuilder;
use Uphf\GestionAbsence\Model\Entity\Absence\StateAbs;
use InvalidArgumentException;
use PDO;

/**
 * Cette classe permet l'insertion d'un justificatif dans la BDD.
 */
class JustificationInsertor {
    /**
     * Insérer un justificatif dans la base de données
     *
     * @param int $idStudent
     * @param string $cause
     * @param string $startDate
     * @param string $endDate
     * @param array $files
     * @return void
     * @throws InvalidArgumentException S'il n'y a pas d'absence pouvant être justifié dans la période sélectionné
     */
    public static function insert(int $idStudent, string $cause, string $startDate, string $endDate, array $files): void {
        // Récupère la connexion et définie une transaction
        $conn = Connection::getInstance();
        $conn->beginTransaction();

        // Récupérer les absences justifiables sur le période sélectionné.
        $absences = new AbsenceSelectBuilder()->idStudent($idStudent)->dateStart($startDate)->dateEnd($endDate)->lock(false)->execute();

        // S'il n'y a pas d'absence justifiable sur le période sélectionné, levé une exception
        if(count($absences) == 0) {
            throw new InvalidArgumentException("Il n'y a pas d'absence pouvant être justifié dans la période sélectionné");
        }

        // Insérer le justificatif
        $query = "INSERT INTO Justification(cause, currentState, startDate, endDate, sendDate)
                  VALUES (:cause, 'NotProcessed', :startDate, :endDate, now())
                  RETURNING idJustification;";

        $sql = $conn->prepare($query);
        $sql->bindValue(':cause', $cause, PDO::PARAM_STR);
        $sql->bindValue(':startDate', $startDate, PDO::PARAM_STR);
        $sql->bindValue(':endDate', $endDate, PDO::PARAM_STR);
        $sql->execute();

        $idJustification = $sql->fetchColumn();

        // Mettre à jour les absences (Ne peut plus être justifié + état Pending)
        $absenceUpdater = new AbsenceUpdateBuilder();
        foreach ($absences as $absence) {
            $absenceUpdater->loadAbsence($absence)->state(StateAbs::Pending)->allowedJustification(false);
        }
        $absenceUpdater->execute();

        // Remplir la table "AbsenceJustification" et "File"
        self::insertAbsenceJustification($idJustification, $absences, $conn);
        if(!empty($files)) {
            self::insertFiles($idJustification, $files, $conn);
        }

        $conn->commit();
    }

    /**
     * Insérer les liaisons entre les absences et le justificatif
     *
     * @param int $idJustification
     * @param array $absences
     * @param PDO $conn
     * @return void
     */
    private static function insertAbsenceJustification(int $idJustification, array $absences, PDO $conn): void {
        $insert = [];
        $parameters = [
            "idJustification" => [$idJustification, PDO::PARAM_INT],
            "idStudent" => [$absences[0]->getIdAccount(), PDO::PARAM_INT]
        ];

        for($i = 0; $i < count($absences); $i++) {
            $insert[] = "(:idStudent, :time$i, :idJustification)";
            $parameters["time$i"] = [$absences[$i]->getTime()->format("Y-m-d H:i:s"), PDO::PARAM_STR];
        }

        $query = "INSERT INTO AbsenceJustification
                  VALUES " . implode(", ", $insert);

        $sql = $conn->prepare($query);

        foreach ($parameters as $key => [$value, $type]) {
            $sql->bindValue(":$key", $value, $type);
        }

        $sql->execute();
    }

    /**
     * Insérer les fichiers liés au justificatif
     *
     * @param int $idJustification
     * @param array $files
     * @param PDO $conn
     * @return void
     */
    private static function insertFiles(int $idJustification, array $files, PDO $conn): void {
        $insert = [];
        $parameters = ["idJustification" => [$idJustification, PDO::PARAM_INT]];

        for($i = 0; $i < count($files); $i++) {
            $insert[] = "(:filename$i, :idJustification)";
            $parameters["filename$i"] = [$files[$i]["name"], PDO::PARAM_STR];
        }

        $query = "INSERT INTO File(filename, idJustification)
                  VALUES " . implode(", ", $insert);

        $sql = $conn->prepare($query);

        foreach($parameters as $key => [$value, $type]) {
            $sql->bindValue(":$key", $value, $type);
        }

        $sql->execute();
    }
}
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . "/../../../../vendor/autoload.php";

JustificationInsertor::insert(2, "Malade", "2025-10-03", "2025-10-03", ["a.png"]);
*/