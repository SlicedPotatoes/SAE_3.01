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

    static public function getAbsencesStudentFiltred ($studentId=null, $startDate=null, $endDate=null, $examen=null, $allowedJustification=null, $stateId=null)
    {
        global $connection;
        $parameters = array();
        $query = "select * from absence";

        $hasStudentId = (isset($studentId));
        $hasStartDate = (isset($startDate));
        $hasEndDate = (isset($endDate));
        $hasStateId = (isset($stateId));

        if ($hasStudentId)
        {
            $parameters["studentId"] = $studentId;
            $query .= " where idStudent = :studentId";
        }

        if ($hasStartDate)
        {
            $parameters["dateDebut"] = $startDate;
            $query .= " INTERSECT select * from absence where time >= :dateDebut";
        }

        if ($hasEndDate)
        {
            $parameters["dateFin"] = $endDate;
            $query .= " INTERSECT select * from absence where time <= :dateFin";
        }

        if ($examen)
        {
            $query .= " INTERSECT select * from absence where examen = true";
        }

        if ($allowedJustification)
        {
            $query .= " INTERSECT select * from absence where allowedJustification = true";
        }

        if ($hasStateId)
        {
            $parameters["stateId"] = $stateId;
            $query .= " INTERSECT select * from absence where idstate = :stateId";
        }

        echo $query;
        var_dump($parameters);

        $request = $connection->prepare($query);

        foreach ($parameters as $key => $value)
        {
            $request->bindValue(':'.$key, $value);
        }

        $request->execute();
        $rows = $request->fetchAll(PDO::FETCH_ASSOC);

        var_dump($rows);

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