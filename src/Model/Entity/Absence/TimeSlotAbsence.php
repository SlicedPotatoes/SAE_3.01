<?php

namespace Uphf\GestionAbsence\Model\Entity\Absence;

use DateTime;
use Uphf\GestionAbsence\Model\Entity\Account\Student;
use Uphf\GestionAbsence\Model\Entity\Account\Teacher;

class TimeSlotAbsence
{
    private DateTime $time;
    private bool $examen;
    private string $duration;
    private int $countStudentsAbsences;
    private int $countStudentsAbsencesJustified;
    private Teacher $teacher;
    private CourseType $courseType;
    private string $groupe;
    private Resource $resource;
    private array|null $studentAbsencesList;

    /**
     * @param DateTime $time
     * @param bool $examen
     * @param string $duration
     * @param int $countStudentsAbsences
     * @param int $countStudentsAbsencesJustified
     * @param Teacher $teacher
     * @param CourseType $courseType
     * @param string $group
     * @param Resource $resource
     */
    public function __construct(DateTime $time, bool $examen, string $duration, int $countStudentsAbsences, int $countStudentsAbsencesJustified, Teacher $teacher, CourseType $courseType, string $groupe, Resource $resource)
    {
        $this->time = $time;
        $this->examen = $examen;
        $this->duration = $duration;
        $this->countStudentsAbsences = $countStudentsAbsences;
        $this->countStudentsAbsencesJustified = $countStudentsAbsencesJustified;
        $this->teacher = $teacher;
        $this->courseType = $courseType;
        $this->groupe = $groupe;
        $this->resource = $resource;
    }

    public function getTime(): DateTime
    {
        return $this->time;
    }

    public function isExamen(): bool
    {
        return $this->examen;
    }

    public function getDuration(): string
    {
        return $this->duration;
    }

    public function getCountStudentsAbsences(): int
    {
        return $this->countStudentsAbsences;
    }

    public function getCountStudentsAbsencesJustified(): int
    {
        return $this->countStudentsAbsencesJustified;
    }

    public function getTeacher(): Teacher
    {
        return $this->teacher;
    }

    public function getCourseType(): CourseType
    {
        return $this->courseType;
    }

    public function getGroupe(): string
    {
        return $this->groupe;
    }

    public function getResource(): Resource
    {
        return $this->resource;
    }

    public function getStudentAbsencesList(): ?array
    {
        return $this->studentAbsencesList;
    }


}