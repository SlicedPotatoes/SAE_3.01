<?php

namespace Uphf\GestionAbsence\ViewModel;

use Uphf\GestionAbsence\Model\Entity\Account\Student;

readonly class StudentStatisticsViewModel extends BaseViewModel
{
    public HeaderViewModel $headerVM;
    public Student $student;
    public string $studentFirstName;
    public string $studentLastName;

    public function __construct(Student $student ) {
        $this->studentFirstName = $student->getFirstName();
        $this->studentLastName = $student->getLastName();
        $this->headerVM = new HeaderViewModel(false, "Vous pouvez observer les différentes", "Statistiques", "de l'étudiant");
    }
}