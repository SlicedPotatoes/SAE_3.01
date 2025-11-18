<?php

namespace Uphf\GestionAbsence\ViewModel;

use Uphf\GestionAbsence\Model\Entity\Account\Student;

/**
 * View model pour stocker un étudiant, utilisé dans d'autre view model
 */
readonly class OneStudentViewModel extends BaseViewModel {
    public string $fullname;
    public string $groupStudent;
    public int $studentNumber;
    public int $idAccount;

    public function __construct(Student $student) {
        $this->fullname = $student->getFirstName() . " " . $student->getLastName();
        $this->groupStudent = $student->getGroupStudent()->getLabel();
        $this->studentNumber = $student->getStudentNumber();
        $this->idAccount = $student->getIdAccount();
    }
}