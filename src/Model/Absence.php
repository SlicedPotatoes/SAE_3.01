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
    private DateInterval $duration; // A voir comment gérer ca
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
    public function getDuration(): DateInterval { return $this->duration; }
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
     * TODO: Connecter a la base de données, faire en sorte qu'elle sois paramétrique, avec systeme de filtre / trie (?)
    */
    public static function getAbsences(): array {
        //$student, $time, $duration, $examen, $allowedJustification, $teacher, $currentState, $courseType, $resource, $dateResit
        return [
            new Absence(
                null,
                (new DateTime())->setDate(2025, 9, 20)->setTime(9, 30),
                new DateInterval("PT1H30M"),
                true,
                false,
                null,
                StateAbs::Refused,
                CourseType::CM,
                null,
                null
            ),
            new Absence(
                null,
                (new DateTime())->setDate(2025, 9, 20)->setTime(8, 0),
                new DateInterval("PT1H30M"),
                true,
                true,
                null,
                StateAbs::NotJustified,
                CourseType::CM,
                null,
                null
            ),
            new Absence(
                null,
                (new DateTime())->setDate(2025, 9, 25)->setTime(11, 0),
                new DateInterval("PT1H30M"),
                false,
                true,
                null,
                StateAbs::Validated,
                CourseType::TD,
                null,
                null
            ),
            new Absence(
                null,
                (new DateTime())->setDate(2025, 9, 25)->setTime(14, 0),
                new DateInterval("PT1H30M"),
                false,
                true,
                null,
                StateAbs::Pending,
                CourseType::TD,
                null,
                null
            )
        ];
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
            $where[] = "studentId = :studentId";
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

        if ($examen !== null)
        {
            $where[] = "examen = :examen";
            $parameters["examen"] = $examen;
        }

        if ($allowedJustification !== null)
        {
            $where[] = "allowedJustification = :allowedJustification";
            $parameters["allowedJustification"] = $allowedJustification;
        }

        if (!empty($where))
        {
            $query .= " where " . implode(" and ", $where);
        }

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
            $absences[] = new Absence($r['id'], $r['time'], $r['duration'], $r['examen'],
                $r['state'], $r['courseType'], $r['ressource'], $r['teacher'], $r['student'],
                $r['allowedJustification']);
        }

        return $absences;
    }
}