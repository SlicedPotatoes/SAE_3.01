<?php
require_once "StateJustif.php";
require_once "Absence.php";
class Justification {
    private int $idJustification;
    private string $cause;
    private StateJustif $currentState;
    private ?DateTime $startDate;
    private ?DateTime $endDate;
    private ?DateTime $sendDate;
    private ?DateTime $processedDate;
    private array $files;
    private array $absences;

    public function __construct($idJustification, $cause, $currentState, $startDate, $endDate, $sendDate, $processedDate)
    {
        $this->idJustification = $idJustification;
        $this->cause = (string)$cause;
        $this->currentState = $currentState;

        // parser les dates de façon robuste
        $this->startDate = $this->parseDateOrNull($startDate);
        $this->endDate = $this->parseDateOrNull($endDate);
        $this->sendDate = $this->parseDateOrNull($sendDate);
        $this->processedDate = $this->parseDateOrNull($processedDate);

        $this->files = [];
        $this->absences = [];
    }

    private function parseDateOrNull($value): ?DateTime {
        if ($value instanceof DateTime) return $value;
        if ($value === null) return null;
        if (is_string($value) && trim($value) !== '') {
            try {
                // essayer parsing flexible
                return new DateTime($value);
            } catch (Exception $e) {
                return null;
            }
        }
        return null;
    }

    public function getIdJustification(): int { return $this->idJustification; }
    public function getCause(): string { return $this->cause; }
    public function getCurrentState(): StateJustif { return $this->currentState; }
    public function getStartDate(): ?DateTime { return $this->startDate; }
    public function getEndDate(): ?DateTime { return $this->endDate; }
    public function getSendDate(): ?DateTime { return $this->sendDate; }
    public function getProcessedDate(): ?DateTime { return $this->processedDate; }


    /*
    Cette fonction sert à récupérer les noms des fichiers.
    Si la liste est vide, une requête est effectuée dans la base de données pour les récupérer.
    */
    public function getFiles(): array {
        if(count($this->files) == 0) {
            global $connection;
            if (!isset($connection) || !$connection) return $this->files;
            $query = $connection->prepare("SELECT * FROM file WHERE idjustification = :idJustification");
            $query->bindParam(":idJustification", $this->idJustification);
            $query->execute();
            $files = $query->fetchAll();
            foreach($files as $file) {
                // utiliser le constructeur File existant si présent
                if (class_exists('File')) {
                    $this->files[] = new File($file["idfile"] ?? $file['idFile'] ?? null, $file["filename"] ?? $file['url'] ?? null, $this);
                } else {
                    $this->files[] = $file;
                }
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
            global $connection;
            if (!isset($connection) || !$connection) return $this->absences;
            $query = $connection->prepare("SELECT * FROM absence join absencejustification using (idstudent,time) WHERE idjustification = :idJustification");
            $query->bindParam(":idJustification", $this->idJustification);
            $query->execute();
            $absences = $query->fetchAll();
            foreach($absences as $absence) {
                $this->absences[] = new Absence(null,
                    DateTime::createFromFormat("Y-m-d H:i:s", $absence["time"]) ?: new DateTime($absence["time"]),
                    $absence["duration"],
                    $absence["examen"],
                    $absence["allowedjustification"],
                    null,
                    StateAbs::from($absence['currentstate']),
                    CourseType::from($absence['coursetype']),
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
        $query = "INSERT INTO justification(cause,currentstate,startdate,enddate,senddate) VALUES (:cause,'NotProcessed' , :startDate, :endDate,now()) RETURNING idjustification;";
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

            $query = "INSERT INTO absencejustification VALUES(:idStudent,:time,:idJustification)";
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
            $query = "INSERT INTO file(filename,idjustification) VALUES(:filename, :justification)";
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

        $query = "SELECT DISTINCT idjustification, cause, currentstate, startdate, enddate, senddate, processeddate 
        FROM justification join absencejustification using (idjustification)";

        if($idStudent != null)
        {
            $parameters['idStudent'] = $idStudent;
            $query .= " WHERE idstudent = :idStudent";
        }
        if($startDate != null)
        {
            $query .= " and enddate >= :startDate";
            $parameters["startDate"] = $startDate;
        }
        if($endDate != null)
        {
            $query .= " and startdate <= :endDate";
            $parameters["endDate"] = $endDate;
        }
        if($currentState != null){
            $query .= " and currentstate = :currentState";
            $parameters["currentState"] = $currentState;
        }
        if (!empty($where))
        {
            $query .= " where " . implode(" and ", $where);
        }
        if($examen)
        {
            $query .= " INTERSECT SELECT DISTINCT idjustification, cause, j.currentstate, startdate, enddate, senddate, processeddate
            FROM absence a join absencejustification using (idstudent,time)
            join justification j using(idjustification)
            where examen = true";
        }

        $query .= " ORDER BY senddate DESC";

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
                (isset($justification["startdate"]) && $justification["startdate"] !== null) ? (new DateTime($justification["startdate"])) : null,
                (isset($justification["enddate"]) && $justification["enddate"] !== null) ? (new DateTime($justification["enddate"])) : null,
                (isset($justification["senddate"]) && $justification["senddate"] !== null) ? (new DateTime($justification["senddate"])) : null,
                isset($justification["processeddate"]) && $justification["processeddate"] !== null ? (new DateTime($justification["processeddate"])) : null
            );
        }
        return $justifications;
    }
}