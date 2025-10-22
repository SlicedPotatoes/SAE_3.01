<?php
require_once "connection.php";

require_once "StateAbs.php";
require_once "CourseType.php";
require_once "Student.php";
require_once "Teacher.php";
require_once "Resource.php";

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

    public function getIdStudent():int {
        if(gettype($this->student) != 'integer') {
            return $this->student->getStudentId();
        }

        return $this->student;
    }

    //    Getter de base
    public function getTime(): DateTime { return $this->time; }
    public function getDuration(): string { return $this->duration; }
    public function getExamen(): bool { return $this->examen; }
    public function getAllowedJustification(): bool { return $this->allowedJustification; }
    public function getTeacher(): Teacher { return $this->teacher; }
    public function getCurrentState(): StateAbs { return $this->currentState; }
    public function getCourseType(): CourseType { return $this->courseType; }
    public function getResource(): Resource { return $this->resource; }
    public function getDateResit(): DateTime { return $this->dateResit; }
    public function getJustifications(): array {
        if(count($this->justifications) == 0) {
            // TODO: Requête SQL
        }
        return $this->justifications;
    }

    /*
     * Mes a jour la collonne allowedJustification dens la bace 2 donner
     * Et localement sur l'objet
     */
    public function setAllowedJustification($value): void {
        global $connection;
        $query = "UPDATE Absence SET allowedJustification = :value WHERE idStudent = :idStudent AND time = :time";
        $row = $connection->prepare($query);

        $idStudent = $this->getIdStudent();
        $dateString = $this->time->format('Y-m-d H:i:s');

        $row->bindParam('value', $value, PDO::PARAM_BOOL);
        $row->bindParam('idStudent', $idStudent);
        $row->bindParam('time', $dateString);
        $row->execute();

        $this->allowedJustification = $value;
    }

        /*
         * Mes a jour la collonne state dans la base de données
         * Et localement sur l'objet
         */    public function setState($state): void {
        global $connection;
        $query = "UPDATE Absence SET currentState = :value WHERE idStudent = :idStudent AND time = :time";
        $row = $connection->prepare($query);

        $idStudent = $this->getIdStudent();
        $dateString = $this->time->format('Y-m-d H:i:s');

        $row->bindParam('value', $state);
        $row->bindParam('idStudent', $idStudent);
        $row->bindParam('time', $dateString);
        $row->execute();

        $this->currentState = StateAbs::from($state);
    }

    /**
     * Recherche d’absences avec filtres optionnels.
     *
     * - Fenêtre de dates incluse.
     * - Si $examen = true, on restreint aux absences d’examen ; sinon on ne filtre pas sur examen.
     * - Si $state est fourni, on filtre exactement cet état.
     * - Si $locked, on restreint à allowedJustification = false AND currentState IN ('Refused','NotJustified').
     *
     * Retour : liste d’objets Absence filtré et trié par time DESC.
     */
    static public function getAbsencesStudentFiltered (
        int | null    $studentId,
        string | null $startDate,
        string | null $endDate,
        bool          $examen,
        bool          $locked,
        string | null $state): array
    {
        global $connection;


        // TODO ajouter les jointure pour : récup la ressource et le nom du prof
        $query = "select absence.*, Resource.label, Account.lastname from absence
                    join Resource using (idResource)
                    join Account on idteacher = idAccount";

        $parameters = array(); // valeurs à binder sur la requête préparée
        $where = array(); // conditions SQL

        // Filtre par étudiant si fourni
        if ($studentId !== null)
        {
            $where[] = "idstudent = :studentId";
            $parameters["studentId"] = $studentId;
        }

        if ($startDate !== null)
        {
            $where[] = "time >= :startDate";
            $parameters["startDate"] = $startDate;
        }

        if ($endDate !== null)
        {
            $where[] = "time <= cast(:endDate as date) + interval '1 day'";
            $parameters["endDate"] = $endDate;
        }

        // Si $state = true, on limite strictement au state sinon on ne filtre pas
        if ($state !== null)
        {
            $where[] = "currentState = :state";
            $parameters["state"] = $state;
        }

        // Si $examen = true, on limite aux absences d'examen sinon on ne filtre pas
        if ($examen)
        {
            $where[] = "examen = true";
        }

        // Si $locked = true, on limite aux absences qui sont vérouillé parmis les refusés et les non-justifiée
        if ($locked)
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

        //TODO créé les objets de type Teacher et de type Ressource
        foreach ($rows as $r)
        {
            var_dump($r);
            $absences[] = new Absence(
                $r['idstudent'],
                DateTime::createFromFormat("Y-m-d H:i:s", $r['time']),
                $r['duration'],
                $r['examen'],
                $r['allowedjustification'],

                //TODO ajout teacher
                $r['lastname'],

                StateAbs::from($r['currentstate']),
                CourseType::from($r['coursetype']),

                //TODO
                $r['label'],


                (isset($r['dateresit']) ? DateTime::createFromFormat("Y-m-d H:i:s", $r['dateresit']) : null)
            );
        }

        return $absences;
    }

}