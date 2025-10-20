<?php
require_once "StateJustif.php";
require_once "Absence.php";
class Justification {
    private int $idJustification;
    private string $cause;
    private StateJustif $currentState;
    private DateTime $startDate;
    private DateTime $endDate;
    private DateTime $sendDate;
    private DateTime|null $processedDate;
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
    public function getCause(): string { return $this->cause; }
    public function getCurrentState(): StateJustif { return $this->currentState; }
    public function getStartDate(): DateTime { return $this->startDate; }
    public function getEndDate(): DateTime { return $this->endDate; }
    public function getSendDate(): DateTime { return $this->sendDate; }
    public function getProcessedDate(): DateTime { return $this->processedDate; }
    /*
    Cette fonction sert à récupérer les noms des fichiers.
    Si la liste est vide, une requête est effectuée dans la base de données pour les récupérer.
    */
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
    /*
    Cette fonction sert à récupérer les absences stockées dans le justificatif. S’il n’y a pas d’absences enregistrées,
    elle récupère celles qui sont liées au justificatif dans la base de données.
    */
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
    Cette fonction sert à insérer les données d’un justificatif dans la base de données, à lier les justificatifs aux absences,
    à insérer des fichiers dans la base de données et à les associer au justificatif.
     */
    static public function insertJustification($idStudent, $cause, $startDate, $endDate, $files): bool
    {
        //Récupération de la connexion
        global $connection;
        //Récupération des absences comprise entre startDate et endDate
        $absences = Absence::getAbsencesStudentFiltered($idStudent, $startDate, $endDate, false, false, null);

        $countAbs = 0;

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
            if(!$absence->getAllowedJustification()) {
                continue;
            }

            $timeAbs = $absence->getTime()->format('Y-m-d H:i:s');

            $query = "INSERT INTO absenceJustification VALUES(:idStudent,:time,:idJustification)";
            $row = $connection->prepare($query);
            $row->bindParam('idStudent', $idStudent);
            $row->bindParam('time', $timeAbs);
            $row->bindParam('idJustification', $idJustification);
            $row->execute();

            $absence->setState(StateAbs::Pending->value);
            $absence->setAllowedJustification(false);

            $countAbs++;
        }

        if($countAbs == 0) {
            // TODO: Annuler les changements
            return false;
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

        return true;
    }
    /*
    Cette fonction sert à récupérer les justificatifs stockés dans la base de données, filtrés à l’aide des différentes variables d’entrée.
    Si la variable examen est définie sur false, alors la fonction renverra tous les justificatifs en ignorant les contraintes liés aux examens.
    */
    public static function selectJustification($idStudent,$startDate,$endDate,$currentState,$examen): array
    {
        //Récupération de la connexion et déclaration de variable
        global $connection;
        $justifications = array();
        $parameters = array();

        //Requête avec système de filtre

        $query = "SELECT DISTINCT idJustification, cause, currentState, startDate, endDate, sendDate, processedDate 
        FROM justification join absenceJustification using (idJustification)";

        if($idStudent != null)
        {
            $parameters['idStudent'] = $idStudent;
            $query .= " WHERE idStudent = :idStudent";
        }
        if($startDate != null)
        {
            $query .= " and endDate >= :startDate";
            $parameters["startDate"] = $startDate;
        }
        if($endDate != null)
        {
            $query .= " and startdate <= :endDate";
            $parameters["endDate"] = $endDate;
        }
        if($currentState != null){
            $query .= " and currentState = :currentState";
            $parameters["currentState"] = $currentState;
        }
        if (!empty($where))
        {
            $query .= " where " . implode(" and ", $where);
        }
        if($examen)
        {
            $query .= " INTERSECT SELECT DISTINCT idJustification, cause, j.currentState, startDate, endDate, sendDate, processedDate
            FROM absence a join absenceJustification using (idStudent,time)
            join justification j using(idJustification)
            where examen = true";
        }

        $query .= " ORDER BY sendDate DESC";

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
            $justifications[] = new Justification(
                $justification["idjustification"],
                $justification["cause"],
                StateJustif::from($justification["currentstate"]),
                DateTime::createFromFormat("Y-m-d H:i:s", $justification["startdate"]),
                DateTime::createFromFormat("Y-m-d H:i:s", $justification["enddate"]),
                DateTime::createFromFormat("Y-m-d H:i:s.u", $justification["senddate"]),
                isset($justification["processeddate"]) ? DateTime::createFromFormat("Y-m-d H:i:s.u",
                $justification["processeddate"]) : null
            );
        }
        return $justifications;
    }

    /*
     * Cette fonction sert à basculer l'état du justificatif et
     * appliquer le nouvel état dans l'objet courant et dans la base de données.
     */
    function changeStateJustification(): void
    {
        //Connexion à la base de données
        global $connection;

        //Requête SQL pour changer la valur dans la base de données
        $query = "update justification
        set currentState = :currentState
        where idJustification = :idJustification";
        $row = $connection->prepare($query);
        $row->bindParam('idJustification', $this->idJustification);

        //Changement selon l'état du justificatif
        if($this->currentState == StateJustif::NotProcessed)
        {
            $this->currentState = StateJustif::Processed;
            $temp = StateJustif::Processed->value;
            $row->bindParam('currentState', $temp);
            $row->execute();

        }else
        {
            $this->currentState = StateJustif::NotProcessed;
            $temp = StateJustif::NotProcessed->value;
            $row->bindParam('currentState', $temp);
            $row->execute();
        }
    }
}