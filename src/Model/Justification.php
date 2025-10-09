<?php
require_once "StateJustif.php";
class   Justification {
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
            global $connexion;
            $query = $connexion->prepare("SELECT * FROM files WHERE idJustification = :idJustification");
            $query->bindParam(":idJustification", $this->idJustification);
            $query->execute();
            $files = $query->fetchAll();
            foreach($files as $file) {
                $this->files[] = new File($file["idFile"], $file["url"], $this);
            }
        }
        return $this->files;
    }
    public function getAbsences(): array {
        if(count($this->absences) == 0) {
            global $connexion;
            $query = $connexion->prepare("SELECT * FROM absence join absenceJustification using (idStudent,time) WHERE idJustification = :idJustification");
            $query->bindParam(":idJustification", $this->idJustification);
            $query->execute();
            $absences = $query->fetchAll();
            foreach($absences as $absence) {
                $this->absences[] = new Absence(null,
                    $absence["time"],
                    $absence["duration"],
                    $absence["examen"],
                    $absence["allowedJustification"],
                    null,
                    StateAbs::from($absence['currentState']),
                    CourseType::from($absence['courseType']),
                    null,
                    (isset($absence['dateresit']) ? DateTime::createFromFormat("Y-m-d H:i:s", $absence['dateresit']) : null));
            }
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

    static public function insertJustification($idStudent, $cause, $startDate, $endDate, $files)
    {
        //Récupération de la connexion
        global $connection;
        //Récupération des absences comprise entre startDate et endDate
        $absences = Absence::getAbsencesStudentFiltered($idStudent, $startDate, $endDate, false, true, null);

        //Insertion des données dans 'justification' et récupération de l'ID créé
        $query = "INSERT INTO justification(cause,currentState,startDate,endDate,sendDate) VALUES (:cause,'NotProcessed' , :startDate, :endDate,now()) RETURNING idJustification;";
        $row = $connection->prepare($query);
        $row->bindParam('cause', $cause);
        $row->bindParam('startDate', $startDate);
        $row->bindParam('endDate', $endDate);
        $row->execute();
        $idJustification = $row->fetchColumn();

        //Liaison des absences avec justifications
        foreach ($absences as $absence)
        {
            $query = "INSERT INTO absenceJustification VALUES(:idStudent,:time,:idAbsence))";
            $row = $connection->prepare($query);
            $row->bindParam('idStudent', $idStudent);
            $row->bindParam('time', $absence->getTime());
            $row->bindParam('idAbsence', $absence->getIdAbsence());
            $row->execute();
        }

        //Insertion des fichiers et liaison à un idJustification
        foreach ($files as $file)
        {
            $query = "INSERT INTO file(filename,idJustification) VALUES(:filename, :justification)";
            $row = $connection->prepare($query);
            $row->bindParam('filename', $file);
            $row->bindParam('justification', $idJustification);
            $row->execute();
        }
    }

    public static function selectJustification($idStudent,$startDate,$endDate,$currentState,$examen)
    {
        //Récupération de la connexion et déclaration de variable
        global $connection;
        $justifications = array();
        $parameters = array();

        //Requête avec système de filtre
        $query = "SELECT idJustification, cause, currentState, startDate, endDate, sendDate, processedDate FROM justification join absenceJustification using (idJustification)";
        if($idStudent != null)
        {
            $parameters['idStudent'] = $idStudent;
            $query .= " WHERE idStudent = :idStudent";
        }
        if($startDate != null)
        {
            $query .= " and startdate >= :startDate";
            $parameters["startDate"] = $startDate;
        }
        if($endDate != null)
        {
            $query .= " and startdate <= :endDate";
            $parameters["endDate"] = $endDate;
        }
        if($currentState != null){
            $query .= " and idstate = :currentState";
            $parameters["currentState"] = $currentState;
        }
        if (!empty($where))
        {
            $query .= " where " . implode(" and ", $where);
        }
        if($examen)
        {
            $query .= " INTERSECT SELECT idJustification, cause, currentState, startDate, endDate, sendDate, processedDate
            FROM absence join absenceJustification using (idStudent,time)
            join justification using(idJustification)
            WHERE idstudent = :idStudent and examen = true";
        }
        $row = $connection->prepare($query);
        foreach ($parameters as $key => $value)
        {
            $row->bindValue(':'.$key, $value);
        }
        $row->execute();
        $result = $row->fetchAll();

        //Mise en objet du résultat et retour du résultat
        foreach ($result as $justification)
        {
            $justifications[] = new Justification($justification["idJustification"], $justification["cause"], $justification["currentState"], $justification["startDate"], $justification["endDate"], $justification["sendDate"], $justification["processedDate"]);
        }
        return $justifications;
    }
}