<?php
require_once "GroupStudent.php";
require_once "connection.php";
class Student
{
    private int $studentId;
    private string $lastName;
    private string $firstName;
    private null | string $firstName2;
    private null | string $email;
    private null | GroupStudent $groupStudent;

    private NULL | int $absTotal;
    private NULL | int $absValidated;
    private NULL | int $absPending;
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

        $this->absTotal = null;
        $this->absValidated = null;
        $this->absPending = null;
        $this->absences = [];
        $this->justifications = [];
    }

    // Quand on passe un object dans une session, PHP fait appel a __serialize() pour le stocké
    public function __serialize(): array {
        return [
            'studentId' => $this->studentId,
            'lastName' => $this->lastName,
            'firstName' => $this->firstName,
            'firstName2' => $this->firstName2,
            'email' => $this->email,
            'groupStudent' => $this->groupStudent,
        ];
    }

    // Quand on initialise une session, et que celle-ci contenait un object, PHP fait appel a __unserialize pour recréer l'object
    public function __unserialize(array $data): void {
        $this->studentId = (int)$data['studentId'];
        $this->lastName = $data['lastName'];
        $this->firstName = $data['firstName'];
        $this->firstName2 = $data['firstName2'];
        $this->email = $data['email'];
        $this->groupStudent = $data['groupStudent'];

        $this->absTotal = null;
        $this->absValidated = null;
        $this->absPending = null;
        $this->absences = [];
        $this->justifications = [];
    }

    // Quand on passe un object dans une session, PHP fait appel a __serialize() pour le stocké
    public function __serialize(): array {
        return [
            'studentId' => $this->studentId,
            'lastName' => $this->lastName,
            'firstName' => $this->firstName,
            'firstName2' => $this->firstName2,
            'email' => $this->email,
            'groupStudent' => $this->groupStudent,
            'absTotal' => $this->absTotal,
            'absValidated' => $this->absValidated,
            'absPending' => $this->absPending,
            'absences' => $this->absences,
            'justifications' => $this->justifications,
        ];
    }

    // Quand on initialise une session, et que celle-ci contenait un object, PHP fait appel a __unserialize pour recréer l'object
    public function __unserialize(array $data): void {
        $this->studentId = (int)$data['studentId'];
        $this->lastName = $data['lastName'];
        $this->firstName = $data['firstName'];
        $this->firstName2 = $data['firstName2'];
        $this->email = $data['email'];
        $this->groupStudent = $data['groupStudent'];

        $this->absTotal = $data['absTotal'];
        $this->absValidated = $data['absValidated'];
        $this->absPending = $data['absPending'];

        $this->absences = (array)$data['absences'];
        $this->justifications = (array)$data['justifications'];
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

    public function getAbsTotal(): int
    {
        if ($this->absTotal !== null) {
            return $this->absTotal;
        }

        global $connection;
        $request = $connection->prepare("SELECT COUNT(*) FROM absence WHERE idStudent = ?");
        $request->bindParam(1, $this->studentId);
        $request->execute();
        $result = $request->fetch();
        $this->absTotal = $result[0];
        return $result[0];
    }

    public function getAbsValidated() : int
    {
        if ($this->absValidated !== null) {
            return $this->absValidated;
        }

        global $connection;
        $request = $connection->prepare("SELECT COUNT(*) FROM absence WHERE idStudent = ? AND currentState = 'Validated'");
        $request->bindParam(1, $this->studentId);
        $request->execute();
        $result = $request->fetch();
        $this->absValidated = $result[0];
        return $result[0];
    }

    public function getAbsPending(): int
    {
        if ($this->absPending !== null) {
            return $this->absPending;
        }

        global $connection;
        $request = $connection->prepare("SELECT COUNT(*) FROM absence WHERE idStudent = ? AND currentState = 'Pending'");
        $request->bindParam(1, $this->studentId);
        $request->execute();
        $result = $request->fetch();
        $this->absPending = $result[0];
        return $result[0];

    }

    public function getAbsRefused(): int
    {
        if ($this->absTotal == null) {
            $this->getAbsTotal();
        }

        if ($this->absValidated == null) {
            $this->getAbsValidated();
        }

        if ($this->absPending == null) {
            $this->getAbsPending();
        }

        return $this->absTotal - $this->absValidated - $this->absPending;

    }

    public function malusPoints(): float
    {
        $absRefused = $this->getAbsRefused();

        if ($absRefused >= 5) {
            return $absRefused*0.1;
        }
        return 0;

    }

}
