<?php

namespace Uphf\GestionAbsence\ViewModel;

use Uphf\GestionAbsence\Model\DB\Select\SelectBuilder\ProportionStatisticsType;
use Uphf\GestionAbsence\Model\Entity\Account\Student;

/**
 * View model pour la vue StudentStatistics
 */
readonly class StudentStatisticsViewModel extends BaseViewModel
{
    public HeaderViewModel $headerVM;
    public Student $student;
    public string $studentFirstName;
    public string $studentLastName;
    public array $datas;
    public ProportionStatisticsType $currTab;
    public array $groups;
    public array $filters;

    public function __construct(
        Student $student,
        array $datas,
        ProportionStatisticsType $currTab,
        array $groups,
        array $filters
    ) {
        $this->studentFirstName = $student->getFirstName();
        $this->studentLastName = $student->getLastName();
        $this->headerVM = new HeaderViewModel(false, "Vous pouvez observer les différentes", "Statistiques", "de " . $student->getFirstName() . " " . $student->getLastName());

        $this->datas = $datas;
        $this->currTab = $currTab;
        $this->groups = array_map(fn($g) => [
            'id' => $g->getIdGroupStudent(),
            'label' => $g->getLabel()
        ], $groups);
        $this->filters = $filters;
    }
}