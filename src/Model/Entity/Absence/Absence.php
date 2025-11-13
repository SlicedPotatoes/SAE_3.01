<?php

namespace Uphf\GestionAbsence\Model\Entity\Absence;

use Uphf\GestionAbsence\Model\Entity\Account\Student;
use Uphf\GestionAbsence\Model\Entity\Account\Teacher;
use DateTime;

/**
 * Classe d'Absence, basé sur la base de données.
 */
class Absence {
    // Attribut de base
    private Student $student;
    private DateTime $time;
    private string $duration;
    private bool $examen;
    private bool $allowedJustification;
    private Teacher|null $teacher;
    private StateAbs $currentState;
    private CourseType $courseType;
    private Resource $resource;
    private DateTime|null $dateResit;

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
    }

    // Getter de base
    public function getStudent(): Student { return $this->student; }
    public function getIdAccount():int { return $this->student->getIdAccount(); }
    public function getTime(): DateTime { return $this->time; }
    public function getDuration(): string { return $this->duration; }
    public function getExamen(): bool { return $this->examen; }
    public function getAllowedJustification(): bool { return $this->allowedJustification; }
    public function getTeacher(): null | Teacher { return $this->teacher; }
    public function getCurrentState(): StateAbs { return $this->currentState; }
    public function getCourseType(): CourseType { return $this->courseType; }
    public function getResource(): Resource { return $this->resource; }
    public function getDateResit(): null | DateTime { return $this->dateResit; }

    // Setter de base
    public function setState(StateAbs $state): void { $this->currentState = $state; }
    public function setAllowedJustification(bool $value): void { $this->allowedJustification = $value; }
}