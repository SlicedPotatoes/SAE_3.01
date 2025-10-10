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
    private NULL | int $absCanBeJustified;
    private NULL | int $absNotJustified;
    private NULL | int $absRefused;
    private NULL | int $halfdaysAbsences;
    private NULL | float $malusPoints;
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
        $this->absCanBeJustified = null;
        $this->absNotJustified = null;
        $this->absRefused = null;
        $this->halfdaysAbsences = null;
        $this->malusPoints = null;
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
        $this->absCanBeJustified = null;
        $this->absNotJustified = null;
        $this->absRefused = null;
        $this->halfdaysAbsences = null;
        $this->malusPoints = null;
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

    public function getAbsCanBeJustified(): int {
        if ($this->absCanBeJustified !== null) {
            return $this->absCanBeJustified;
        }

        global $connection;

        $query = "SELECT COUNT(*) FROM absence WHERE idStudent = ? AND allowedJustification = true";

        $request = $connection->prepare($query);
        $request->bindParam(1, $this->studentId);
        $request->execute();
        $result = $request->fetch();
        $this->absCanBeJustified = $result[0];
        return $result[0];
    }
    public function getAbsNotJustified(): int
    {
        if ($this->absNotJustified !== null) {
            return $this->absNotJustified;
        }

        global $connection;
        $request = $connection->prepare("SELECT COUNT(*) FROM absence WHERE idStudent = ? AND currentState = 'NotJustified'");
        $request->bindParam(1, $this->studentId);
        $request->execute();
        $result = $request->fetch();
        $this->absNotJustified = $result[0];
        return $result[0];
    }

    public function getAbsRefused(): int
    {
        if ($this->absRefused !== null) {
            return $this->absRefused;
        }

        global $connection;
        $request = $connection->prepare("SELECT COUNT(*) FROM absence WHERE idStudent = ? AND currentState = 'Refused'");
        $request->bindParam(1, $this->studentId);
        $request->execute();
        $result = $request->fetch();
        $this->absRefused = $result[0];
        return $result[0];

    }

    public function getHalfdaysAbsences(): int
    {
        if ($this->halfdaysAbsences !== null) {
            return $this->halfdaysAbsences;
        }

        global $connection;

        $sql = "
        with view_morning_absences as 
        (
            select a.idStudent, cast(time as date) as day
            from absence a
            where cast(a.time as time) < time '12:30' 
            group by a.idStudent, day
        ),
        view_afternoon_absences as 
        (
            select a.idStudent, cast(time as date) as day
            from absence a
            where cast(a.time as time) >= time '12:30'
            group by idStudent, day
        ),
        view_halfdays_absence as 
        (
        select idstudent, day from view_morning_absences
        union all 
        select idstudent, day from view_afternoon_absences
        )
        
        select count(*)
        from view_halfdays_absence
        where idstudent = :idstudent;
        ";

        $query = $connection->prepare($sql);
        $query->bindValue(':idstudent', $this->studentId, PDO::PARAM_INT);
        $query->execute();

        $this->halfdaysAbsences = (int)$query->fetchColumn();

        return $this->halfdaysAbsences;
    }

    public function getMalusPoints(): float
    {
        if ($this->malusPoints !== null) {
            return $this->malusPoints;
        }

        global $connection;

        $sql = "
        with view_morning_absences as 
        (
            select a.idStudent, cast(time as date) as day
            from absence a
            where cast(a.time as time) < time '12:30' 
                and currentState in ('Refused','NotJustified')
            group by a.idStudent, day
        ),
        view_afternoon_absences as 
        (
            select a.idStudent, cast(time as date) as day
            from absence a
            where cast(a.time as time) >= time '12:30' 
                and currentState in ('Refused','NotJustified')
            group by idStudent, day
        ),
        view_halfdays_absence as 
        (
        select idstudent, day from view_morning_absences
        union all 
        select idstudent, day from view_afternoon_absences
        )
        
        select count(*)
        from view_halfdays_absence
        where idstudent = :idstudent;
        ";

        $query = $connection->prepare($sql);
        $query->bindValue(':idstudent', $this->studentId, PDO::PARAM_INT);
        $query->execute();

        $halfdays = (int)$query->fetchColumn();

        $this->malusPoints = ($halfdays >= 5) ? $halfdays * 0.1 : 0.0;

        return $this->malusPoints;
    }
}
