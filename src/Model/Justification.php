<?php
require_once "StateJustif.php";
class Justification {
    private int $idJustification;
    private string $cause;
    private StateJustif $currentState;
    private DateTime $startDate;
    private DateTime $endDate;
    private DateTime $sendDate;
    private DateTime $processedDate;
    private array $files;
    private array $absences;

    public function __construct($idJustification, $cause, $currentState, $startDate, $endDate, $sendDate, $processedDate)
    {
        $this->idJustification = $idJustification;
        $this->cause = $cause;
        $this->currentState = $currentState;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->sendDate = $sendDate;
        $this->processedDate = $processedDate;

        $this->files = [];
        $this->absences = [];
    }

    public function getIdJustification(): int { return $this->idJustification; }
    public function getCause(): int { return $this->cause; }
    public function getCurrentState(): StateJustif { return $this->currentState; }
    public function getStartDate(): DateTime { return $this->startDate; }
    public function getEndDate(): DateTime { return $this->endDate; }
    public function getSendDate(): DateTime { return $this->sendDate; }
    public function getProcessedDate(): DateTime { return $this->processedDate; }
    public function getFiles(): array {
        if(count($this->files) == 0) {
            // TODO: Requête SQL
        }
        return $this->files;
    }
    public function getAbsences(): array {
        if(count($this->absences) == 0) {
            // TODO: Requête SQL
        }
        return $this->absences;
    }

    /*
     * TODO: Connecter a la base de données, faire en sorte qu'elle sois paramétrique, avec système de filtre / trie (?)
     */
    public static function getJustifications(): array {
        return array(
            new Justification(
                1,
                '',
                StateJustif::NotProcessed,
                (new DateTime())->setDate(2025, 9, 24),
                (new DateTime())->setDate(2025, 9, 25),
                (new DateTime())->setDate(2025, 9, 26),
                (new DateTime())->setDate(2025, 9, 27)
            ),
            new Justification(
                2,
                '',
                StateJustif::Processed,
                (new DateTime())->setDate(2025, 9, 1),
                (new DateTime())->setDate(2025, 9, 3),
                (new DateTime())->setDate(2025, 9, 4),
                (new DateTime())->setDate(2025, 9, 5)
            )
        );
    }

    static public function getJustificationsStudentFiltred($filters = [],$studentId=null, $startDate=null, $endDate=null, $examen=null, $allowedJustification=null, $stateId=null)
    {
        global $connection;
        $parameters = array();


        $query = "SELECT justification.*, absence.*, file.url
              FROM justification
              LEFT JOIN absenceJustification ON justification.idJustification = absenceJustification.idJustification
              LEFT JOIN absence ON absenceJustification.idAbsence = absence.idAbsence
              LEFT JOIN file ON file.idStudentProof = justification.idJustification
              ";


        $hasStudentId = (isset($studentId));
        $hasStartDate = (isset($startDate));
        $hasEndDate = (isset($endDate));
        $hasStateId = (isset($stateId));
        $hasExamen = (isset($examen));


        if ($hasStudentId) {
            $parameters['idStudent'] = $filters['idStudent'];
            $query .= " where idStudent = :studentId";
        }

        if ($hasStartDate) {
            $parameters['startDate'] = $filters['startDate'];
            $query .= " INTERSECT select * from absence where time >= :dateDebut";
        }

        if ($hasEndDate) {
            $parameters['endDate'] = $filters['endDate'];
            $query .= " INTERSECT select * from absence where time <= :dateFin";
        }


        if ($hasExamen) {
            $parameters['examen'] = $filters['examen'];
            $query .= " INTERSECT select * from absence where examen = true";
        }

        if ($allowedJustification) {
            $query .= " INTERSECT select * from absence where allowedJustification = true";
        }

        if ($hasStateId) {
            $parameters['stateId'] = $filters['stateId'];
            $query .= " INTERSECT select * from absence where idstate = :stateId";
        }
        $request = $connection->prepare($query);

        foreach ($parameters as $key => $value) {
            $request->bindValue(':' . $key, $value);
        }

        $request->execute();
        $result = $request->fetchAll();

        var_dump($result);
        echo $query;

        echo "\n proute";
    }

    public function sendJustification($idStudent, $cause, $startDate, $endDate)
    {
        global $connection;
        $query = "INSERT INTO justification(idStudent, cause, start, end, processed) VALUES (:idStudent, :cause, :startDate, :endDate, false) RETURNING idJustification;";

        $row = $connection->prepare($query);
        $row->bindParam('idStudent', $idStudent);
        $row->execute();
        $idJustification = $row->fetchColumn();

        $absences = Absence::getAbsencesStudentFiltred($idStudent, $startDate, $endDate, null, true, null);

        foreach ($absences as $absence)
        {
            $query = "INSERT INTO absenceJustification VALUES(:studentID" .", " .$idJustification .");";
            $statement = $connection->prepare($query);

            $connection->exec($query);
        }

        /*

        TO DO : GESTION DE FICHER DE SES MORTS AVEC LE FAIT QUE ON AJOUTE DANS LA BASE
        DE DONNEE LES LIGNES POUR CHAQUE FICHIERS
        JE NE SAIS PAS SI C'EST MIEUX DE LE FAIRE DIRECTEMENT ICI OU DANS UNE AUTRE FONCTION

        */
    }
}