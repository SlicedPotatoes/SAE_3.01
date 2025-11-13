<?php

namespace Uphf\GestionAbsence\ViewModel;

use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\Model\Entity\Account\Student;

/**
 * ViewModel pour la view StudentProfile
 */
readonly class StudentProfileViewModel extends BaseViewModel{
    public string $lastName;
    public string $firstName;
    public array $absences;
    public array $justifications;
    public int $absenceTotal;
    public int $halfdayTotal;
    public int $absenceAllowJustification;
    public float $malus;
    public float $malusWithoutPending;
    public int $penalizingAbsence;
    public int $halfdayPenalizingAbsence;
    public string $currTab;
    public array $filter;
    public AccountType $roleUser;
    public function __construct(
        Student $student,
        array $absences,
        array $justifications,
        int $absenceTotal,
        int $halfdayTotal,
        int $absenceAllowJustification,
        float $malus,
        float $malusWithoutPending,
        int $penalizingAbsence,
        int $halfdayPenalizingAbsence,
        string $currTab,
        array $filter,
        AccountType $roleUser,
    ) {
        $this->lastName = $student->getLastName();
        $this->firstName = $student->getFirstName();

        $this->absences = array_map(fn($abs) => new OneAbsenceViewModel($abs), $absences);
        $this->justifications = array_map(fn($j) => new OneJustificationViewModel($j, $roleUser, $student), $justifications);

        $this->absenceTotal = $absenceTotal;
        $this->halfdayTotal = $halfdayTotal;
        $this->absenceAllowJustification = $absenceAllowJustification;
        $this->malus = $malus;
        $this->malusWithoutPending = $malusWithoutPending;
        $this->penalizingAbsence = $penalizingAbsence;
        $this->halfdayPenalizingAbsence = $halfdayPenalizingAbsence;

        $this->currTab = $currTab;
        $this->filter = $filter;
        $this->roleUser = $roleUser;
    }
}