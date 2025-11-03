<?php
namespace Uphf\GestionAbsence\Model\Justification;

use Uphf\GestionAbsence\Model\Absence\Absence;
use Uphf\GestionAbsence\Model\Absence\StateAbs;
use Uphf\GestionAbsence\Model\Connection;
use Uphf\GestionAbsence\Model\Filter\FilterAbsence;
use Uphf\GestionAbsence\Model\Filter\FilterJustification;
use DateTime;

/**
 * Classe Justification, basé sur la base de données.
 */
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

    // Getter de base
    public function getIdJustification(): int { return $this->idJustification; }
    public function getCause(): string { return $this->cause; }
    public function getCurrentState(): StateJustif { return $this->currentState; }
    public function getStartDate(): DateTime { return $this->startDate; }
    public function getEndDate(): DateTime { return $this->endDate; }
    public function getSendDate(): DateTime { return $this->sendDate; }
    public function getProcessedDate(): DateTime|null { return $this->processedDate; }

    /**
     * Récupérer les fichiers liés à un justificatif
     *
     * Si c'est le premier appel de cette méthode, récupère les données depuis la base
     *
     * @return File[]
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

    /**
     * Récupérer les absences liées aux justificatifs
     *
     * Si c'est le premier appel de cette méthode, récupère les données depuis la base
     *
     * @return Absence[]
     */
    public function getAbsences(): array {
        if(count($this->absences) == 0) {
            $connection = Connection::getInstance();
            $query = $connection->prepare("SELECT * FROM absenceJustification join absence using(idStudent,time)
            join resource using (idResource) join account on idteacher = idaccount where idJustification = :idJustification");
            $query->bindParam(":idJustification", $this->idJustification);
            $query->execute();
            $absences = $query->fetchAll();
            foreach($absences as $absence) {
                    $this->absences[] = new Absence($absence["idstudent"],
                    DateTime::createFromFormat("Y-m-d H:i:s", $absence["time"]),
                    $absence["duration"],
                    $absence["examen"],
                    $absence["allowedjustification"],
                    new Teacher($absence["idteacher"], $absence["lastname"], $absence["firstname"], $absence["email"]),
                    StateAbs::from($absence['currentstate']),
                    CourseType::from($absence['coursetype']),
                    new Resource($absence["idresource"],$absence["label"]),
                    (isset($absence['dateresit']) ? DateTime::createFromFormat("Y-m-d H:i:s", $absence['dateresit']) : null));
            }
        }
        return $this->absences;
    }

    /**
     * Inserer un justificatif dans la base de données
     *
     * Fais la liaison avec les absences de l'étudiant pouvant être justifié sur le période sélectionné
     *
     * Inserer les fichiers du justificatif
     *
     * S'il n'y a pas d'absence justifiable sur la periode sélectionné, l'insertion est annulé
     *
     * Renvoie true ou false en fonction de si le justificatif a été inséré ou non
     *
     * TODO: Utilisation de Transaction
     * TODO: Enlever les boucle d'insertion en faisant une seul requête
     *
     * @param $idStudent
     * @param $cause
     * @param $startDate
     * @param $endDate
     * @param $files
     * @return bool
     */
    static public function insertJustification($idStudent, $cause, $startDate, $endDate, $files): bool
    {
        //Récupération de la connexion
        $connection = Connection::getInstance();
        //Récupération des absences comprise entre startDate et endDate
        $absences = Absence::getAbsencesStudentFiltered($idStudent, new FilterAbsence(
            $startDate,
            $endDate,
            null,
            false,
            false
        ));

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

            $absence->setState(StateAbs::Pending);
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

    /**
     * Recherche de justificatifs avec filtres optionnels.
     *
     * - Fenêtre de dates incluse.
     * - Si `$examen` = true, on restreint aux justificatifs contenant une absence à un examen ; sinon on ne filtre pas sur examen.
     * - Si `$currentState` est fourni, on filtre exactement cet état.
     *
     * Retour : liste d’objets Justificatifs filtrée et trié par sendDate DESC.
     *
     * @param null | int $idStudent
     * @param FilterJustification $filter
     * @return Justification[]
     */
    public static function selectJustification(null | int $idStudent, FilterJustification $filter): array
    {
        //Récupération de la connexion et déclaration de variable
        $connection = Connection::getInstance();
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
        if($filter->getDateStart() != null)
        {
            $query .= " and endDate >= :startDate";
            $parameters["startDate"] = $filter->getDateStart();
        }
        if($filter->getDateEnd() != null)
        {
            $query .= " and startdate <= :endDate";
            $parameters["endDate"] = $filter->getDateEnd();
        }
        if($filter->getState() != null){
            $query .= " and currentState = :currentState";
            $parameters["currentState"] = $filter->getState();
        }
        if (!empty($where))
        {
            $query .= " where " . implode(" and ", $where);
        }
        if($filter->getExamen())
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
        $connection = Connection::getInstance();

        //Requête SQL pour changer la valur dans la base de données
        $query = "update justification
        set currentState = :currentState
        where idJustification = :idJustification";
        $row = $connection->prepare($query);
        $row->bindParam('idJustification', $this->idJustification);

        //Changement selon l'état du justificatif
        if($this->currentState == StateJustif::NotProcessed) {
            $this->currentState = StateJustif::Processed;
            $value = StateJustif::Processed->value;
        }
        else {
            $this->currentState = StateJustif::NotProcessed;
            $value = StateJustif::NotProcessed->value;
        }

        $row->bindParam('currentState', $value);
        $row->execute();
    }
}