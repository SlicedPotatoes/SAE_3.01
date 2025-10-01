<?php
require_once "GroupStudent.php";
class Student
{
    private int $studentId;
    private string $lastName;
    private string $firstName;
    private string $firstName2;
    private string $email;
    private GroupStudent $groupStudent;

    private array $absences;
    private array $justifications;
    public function __construct($studentId, $lastName, $firstName, $firstName2, $email, $groupStudent)
    {
        $this->studentId = $studentId;
        $this->lastName = $lastName;
        $this->firstName = $firstName;
        $this->firstName2 = $firstName2;
        $this->email = $email;
        $this->groupStudent = $groupStudent;

        $this->absences = [];
        $this->justifications = [];
    }

    public function getStudentId(): int { return $this->studentId; }
    public function getLastName(): string { return $this->lastName; }
    public function getFirstName(): string { return $this->firstName; }
    public function getFirstName2(): string { return $this->firstName2; }
    public function getEmail(): string { return $this->email; }
    public function getGroupStudent(): GroupStudent { return $this->groupStudent; }
    public function getAbsences(): array {
        if(count($this->absences) == 0) {
            // TODO: Requête SQL
        }
        return $this->absences;
    }
    public function getJustifications(): array {
        if(count($this->justifications) == 0) {
            // TODO: Requête SQL
        }
        return $this->justifications;
    }
}