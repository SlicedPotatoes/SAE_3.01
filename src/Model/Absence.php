<?php
require_once "State.php";
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

    public function __construct($id, $time, $duration, $examen, $state, $courseType, $ressource, $teacher, $student) {
        $this->id = $id;
        $this->time = $time;
        $this->duration = $duration;
        $this->examen = $examen;
        $this->state = $state;
        $this->courseType = $courseType;
        $this->ressource = $ressource;
        $this->teacher = $teacher;
        $this->student = $student;
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

    /*
     * TODO: Connecter a la base de données, faire en sorte qu'elle sois paramétrique, avec systeme de filtre / trie (?)
     *
    */
    public static function getAbsences() {
        $state = State::getAbsenceStates();

        return array(
            new Absence(
                0,
                (new DateTime)->setDate(2025, 9, 24),
                "1h30",
                true,
                $state[1],
                "TD",
                "R3.01",
                "J. Vion",
                null
            ),
            new Absence(
                1,
                (new DateTime)->setDate(2025, 9, 24),
                "1h30",
                true,
                $state[2],
                "TD",
                "R3.01",
                "J. Vion",
                null
            ),
            new Absence(
                2,
                (new DateTime)->setDate(2025, 9, 24),
                "1h30",
                true,
                $state[3],
                "TD",
                "R3.01",
                "J. Vion",
                null
            )
        );
    }
}