<?php
require_once "State.php";
require_once("connection.php");
class Absence {
    private $id;
    private $time;
    private $duration;
    private $examen;
    private $state;
    private $courseType;
    private $ressource;
    private $teacher;
    private $student;
    private $allowedJustification;

    public function __construct($id, $time, $duration, $examen, $state, $courseType, $ressource, $teacher, $student,$allowedJustification) {
        $this->id = $id;
        $this->time = $time;
        $this->duration = $duration;
        $this->examen = $examen;
        $this->state = $state;
        $this->courseType = $courseType;
        $this->ressource = $ressource;
        $this->teacher = $teacher;
        $this->student = $student;
        $this->allowedJustification = $allowedJustification;
    }

    public function getId() { return $this->id; }
    public function getTime() { return $this->time; }
    public function getDuration() { return $this->duration; }
    public function getExamen() { return $this->examen; }
    public function getState() { return $this->state; }
    public function getCourseType() { return $this->courseType; }
    public function getRessource() { return $this->ressource; }
    public function getTeacher() { return $this->teacher; }
    public function getStudent() { return $this->student; }
    public function getAllowedJustification() { return $this->allowedJustification; }


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

        $request = $connection->prepare($query);

        foreach ($parameters as $key => $value)
        {
            $request->bindParam($key, $value);
        }

        $request->execute();
        $result = $request->fetchAll();

        //
        // TO DO : TRANSFORMER LES ABSENCES EN OBJECT DE CLASS ABSENCE
        //


        var_dump($result);
        echo $query;
    }
}

Absence::getAbsencesStudentFiltred(1, '2015-01-05', '2018-01-01', true, false, 2);