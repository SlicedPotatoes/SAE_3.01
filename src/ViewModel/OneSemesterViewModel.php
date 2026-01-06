<?php

namespace Uphf\GestionAbsence\ViewModel;

use Uphf\GestionAbsence\Model\Entity\Semester;

/**
 * View model pour stocker un Semester
 */
readonly class OneSemesterViewModel extends BaseViewModel {
    public int $id;
    public string $label;
    public string $startDate;
    public string $endDate;

    public function __construct(Semester $semester) {
        $this->id = $semester->getId();
        $this->label = $semester->getLabel();
        $this->startDate = $semester->getStartDate()->format("Y-m-d");
        $this->endDate = $semester->getEndDate()->format("Y-m-d");
    }
}
