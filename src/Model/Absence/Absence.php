<?php
require_once __DIR__ . "/../connection.php";
require_once __DIR__ . "/StateAbs.php";
require_once __DIR__ . "/CourseType.php";
require_once __DIR__ . "/../Account/Student.php";
require_once __DIR__ . "/../Account/Teacher.php";
require_once __DIR__ . "/Resource.php";
require_once __DIR__ . "/../Filter/FilterAbsence.php";

/**
 * Classe d'Absence, basé sur la base de données.
 */
class Absence {
    // Attribut de base
    private Student|int $student;
    private DateTime $time;
    private string $duration;
    private bool $examen;
    private bool $allowedJustification;
    private Teacher|null $teacher;
    private StateAbs $currentState;
    private CourseType $courseType;
    private Resource|null $resource;
    private DateTime|null $dateResit;

    // Array de la classe
    private array $justifications;

    public function __construct($student, $time, $duration, $examen, $allowedJustification, $teacher, $currentState, $courseType, $resource, $dateResit) {
        $this->student = $student;
        $this->time = $time;
        $this->duration = $duration;
        $this->examen = $examen;
        $this->allowedJustification = $allowedJustification;
        $this->teacher = $teacher;
        $this->currentState = $currentState;
        $this->courseType = $courseType;
        $this->resource = $resource;
        $this->dateResit = $dateResit;

        $this->justifications = [];
    }

    public function getStudent(): Student {
        if(gettype($this->student) == 'integer') {
            // TODO Requete pour récupérer le student
        }

        return $this->student;
    }

    public function getIdAccount():int {
        if(gettype($this->student) != 'integer') {
            return $this->student->getIdAccount();
        }

        return $this->student;
    }

    // Getter de base
    public function getTime(): DateTime { return $this->time; }
    public function getDuration(): string { return $this->duration; }
    public function getExamen(): bool { return $this->examen; }
    public function getAllowedJustification(): bool { return $this->allowedJustification; }
    public function getTeacher(): null | Teacher { return $this->teacher; }
    public function getCurrentState(): StateAbs { return $this->currentState; }
    public function getCourseType(): CourseType { return $this->courseType; }
    public function getResource(): Resource { return $this->resource; }
    public function getDateResit(): null | DateTime { return $this->dateResit; }
    public function getJustifications(): array {
        if(count($this->justifications) == 0) {
            // TODO: Requête SQL
        }
        return $this->justifications;
    }

    /**
     * Mes a jour la colonne allowedJustification dans la BDD
     * Et localement sur l'objet
     *
     * @param bool $value
     */
    public function setAllowedJustification(bool $value): void {
        global $connection;
        $query = "UPDATE Absence SET allowedJustification = :value WHERE idStudent = :idStudent AND time = :time";
        $row = $connection->prepare($query);

        $idStudent = $this->getIdAccount();
        $dateString = $this->time->format('Y-m-d H:i:s');

        $row->bindParam('value', $value, PDO::PARAM_BOOL);
        $row->bindParam('idStudent', $idStudent);
        $row->bindParam('time', $dateString);
        $row->execute();

        $this->allowedJustification = $value;
    }

    /**
     * Mes a jour la colonne state dans la BDD
     * Et localement sur l'objet
     *
     * @param StateAbs $state
     */
    public function setState(StateAbs $state): void {
        global $connection;
        $query = "UPDATE Absence SET currentState = :value WHERE idStudent = :idStudent AND time = :time";
        $row = $connection->prepare($query);

        $idStudent = $this->getIdAccount();
        $dateString = $this->time->format('Y-m-d H:i:s');

        $stateString = $state->value;

        $row->bindParam('value', $stateString);
        $row->bindParam('idStudent', $idStudent);
        $row->bindParam('time', $dateString);
        $row->execute();

        $this->currentState = $state;
    }

    /**
     * Recherche d’absences avec filtres optionnels.
     *
     * - Fenêtre de dates incluse.
     * - Si `$examen` = true, on restreint aux absences d’examen ; sinon on ne filtre pas sur examen.
     * - Si `$state` est fourni, on filtre exactement cet état.
     * - Si `$locked` on restreint à allowedJustification = false AND currentState IN ('Refused','NotJustified').
     *
     * Retour : liste d’objets Absence filtrée et trié par time DESC.
     *
     * @param null | int $studentId
     * @param FilterAbsence $filter
     * @return Absence[]
     */
    static public function getAbsencesStudentFiltered (null | int $studentId, FilterAbsence $filter): array
    {
        global $connection;


        $query = "select * from absence
                    join Resource using (idResource)
                    left join Account on idteacher = idAccount";

        $parameters = array(); // valeurs à binder sur la requête préparée
        $where = array(); // conditions SQL

        // Filtre par étudiant si fourni
        if ($studentId !== null)
        {
            $where[] = "idstudent = :studentId";
            $parameters["studentId"] = $studentId;
        }

        if ($filter->getDateStart() !== null)
        {
            $where[] = "time >= :startDate";
            $parameters["startDate"] = $filter->getDateStart();
        }

        if ($filter->getDateEnd() !== null)
        {
            $where[] = "time <= cast(:endDate as date) + interval '1 day'";
            $parameters["endDate"] = $filter->getDateEnd();
        }

        // Si $state = true, on limite strictement au state sinon on ne filtre pas
        if ($filter->getState() !== null)
        {
            $where[] = "currentState = :state";
            $parameters["state"] = $filter->getState();
        }

        // Si $examen = true, on limite aux absences d'examen sinon on ne filtre pas
        if ($filter->getExamen())
        {
            $where[] = "examen = true";
        }

        // Si $locked = true, on limite aux absences qui sont vérrouillé parmi les refusés et les non-justifiée
        if ($filter->getLocked())
        {
            $where[] = "allowedJustification = false AND (currentState = 'Refused' OR currentState = 'NotJustified')";
        }

        // Construction finale de la requête
        if (!empty($where))
        {
            $query .= " where " . implode(" and ", $where);
        }

        $query .= " ORDER BY time DESC";

        $sql = $connection->prepare($query);

        // Préparation + binding des paramètres
        foreach ($parameters as $key => $value)
        {
            $sql->bindValue(':'.$key, $value);
        }

        $sql->execute();
        $rows = $sql->fetchAll(PDO::FETCH_ASSOC);

        // Initialisation des lignes de la base de données vers des objets Absence
        $absences = [];

        foreach ($rows as $r)
        {
            $absences[] = new Absence(
                $r['idstudent'],
                DateTime::createFromFormat("Y-m-d H:i:s", $r['time']),
                $r['duration'],
                $r['examen'],
                $r['allowedjustification'],
                isset($r['idteacher']) ? new Teacher($r['idteacher'], $r['lastname'], $r['firstname'], $r['email']) : null,
                StateAbs::from($r['currentstate']),
                CourseType::from($r['coursetype']),
                new Resource($r['idresource'], $r['label']),
                (isset($r['dateresit']) ? DateTime::createFromFormat("Y-m-d H:i:s", $r['dateresit']) : null)
            );
        }

        return $absences;
    }

}