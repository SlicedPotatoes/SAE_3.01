<?php

namespace Uphf\GestionAbsence\ViewModel;

use DateTime;
use Uphf\GestionAbsence\Model\Entity\Absence\CourseType;
use Uphf\GestionAbsence\Model\Entity\Absence\Resource;

/**
 * View model pour la vue du détail des crénaux
 */
readonly class DetailPeriodViewModel extends BaseViewModel
{
    public array $absences;
    public DateTime $time;
    public bool $examen;
    public CourseType $courseType;
    public string $group;
    public Resource $ressource;
    public string $teacherName;
    public bool $isTeacher;

    public function __construct(
      array $absences,
      DateTime $time,
      bool $examen,
      CourseType $courseType,
      Resource $ressource,
      string | null $group,
      string | null $teacherName,
      bool $isTeacher=true
    )
    {
        $this->absences = $absences;
        $this->time = $time;
        $this->examen = $examen;
        $this->courseType = $courseType;
        $this->ressource = $ressource;
        $this->group = $group;
        $this->teacherName = $teacherName;
        $this->isTeacher = $isTeacher;
    }
}