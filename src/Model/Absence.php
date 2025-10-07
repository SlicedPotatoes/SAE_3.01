<?php
require_once "connection.php";

require_once "StateAbs.php";
require_once "CourseType.php";
require_once "Student.php";
require_once "Teacher.php";
require_once "Resource.php";
class Absence {
    private Student|null $student;
    private DateTime $time;
    private string $duration; // A voir comment gérer ca
    private bool $examen;
    private bool $allowedJustification;
    private Teacher|null $teacher;
    private StateAbs $currentState;
    private CourseType $courseType;
    private Resource|null $resource;
    private DateTime|null $dateResit;
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

    public function getStudent(): Student { return $this->student; }
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

    static public function getAbsencesStudentFiltered (
        int | null $studentId,
        string | null $startDate,
        string | null $endDate,
        bool $examen,
        bool $allowedJustification,
        string | null $state): array
    {
        global $connection;

        $query = "select * from absence";

        $parameters = array();
        $where = array();

        if ($studentId !== null)
        {
            $where[] = "idstudent = :studentId";
            $parameters["studentId"] = $studentId;
        }

        if ($startDate !== null)
        {
            $where[] = "dateResit >= :startDate";
            $parameters["startDate"] = $startDate;
        }

        if ($endDate !== null)
        {
            $where[] = "dateResit <= :endDate";
            $parameters["endDate"] = $endDate;
        }

        if ($state !== null)
        {
            $where[] = "state = :state";
            $parameters["state"] = $state;
        }

        if ($examen)
        {
            $where[] = "examen = true";
        }

        if ($allowedJustification)
        {
            $where[] = "allowedJustification = true";
        }

        if (!empty($where))
        {
            $query .= " where " . implode(" and ", $where);
        }

        //echo $query;

        $sql = $connection->prepare($query);

        foreach ($parameters as $key => $value)
        {
            $sql->bindValue(':'.$key, $value);
        }

        $sql->execute();
        $rows = $sql->fetchAll(PDO::FETCH_ASSOC);

        $absences = [];
        foreach ($rows as $r)
        {
            $absences[] = new Absence(null,
                DateTime::createFromFormat("Y-m-d H:i:s", $r['time']),
                $r['duration'],
                $r['examen'],
                $r['allowedjustification'],
                null,
                StateAbs::from($r['currentstate']),
                CourseType::from($r['coursetype']),
                null,
                (isset($r['dateresit']) ? DateTime::createFromFormat("Y-m-d H:i:s", $r['dateresit']) : null)
            );
        }

        return $absences;
    }
}