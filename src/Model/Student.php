<?php
require_once "Account.php";
require_once "GroupStudent.php";
require_once "connection.php";

/**
 * Classe de Student, basé sur la base de données.
 */

class Student extends Account {
    //    Attribut de base de la classe
    private int $studentNumber;
    private null | GroupStudent $groupStudent;

    // Attribut métrique, si null pas encore calculé
    private NULL | int $absTotal = null;
    private NULL | int $absCanBeJustified = null;
    private NULL | int $absNotJustified = null;
    private NULL | int $absRefused = null;
    private NULL | int $halfdaysAbsences = null;
    private NULL | float $malusPoints = null;
    private NULL | float $malusPointsWithoutPending = null;

    //    Array de la classe
    private array $absences = [];
    private array $justifications = [];

    // Constante de la classe : évitement des valeurs hasardeuses
    private const MALUS_TRESSHOLD = 5; // Utilisé pour la limite d'affichage du malus
    private const MALUS_POINTS = 0.1; // Utilisé pour la multiplication du malus


    public function __construct($idAccount, $lastName, $firstName, $email, $accountType, $studentNumber, $groupStudent)
    {
        parent::__construct($idAccount, $lastName, $firstName, $email, $accountType);
        $this->studentNumber = $studentNumber;
        $this->groupStudent = $groupStudent;
    }

    // Serialization uniquement des données fixes
    public function __serialize(): array {
        return parent::__serialize() + ['studentNumber' => $this->studentNumber, 'groupStudent' => $this->groupStudent];
    }

    // La classe est réinitialisé avec rafraichissement des données volatiles avec requête SQL
    public function __unserialize(array $data): void {
        parent::__unserialize($data);
        $this->studentNumber = $data['studentNumber'];
        $this->groupStudent = $data['groupStudent'];
    }

    // getter basique
    public function getStudentNumber(): int{return $this->studentNumber;}
    public function getGroupStudent(): GroupStudent { return $this->groupStudent; }

    /**
     * @todo Si survient un besoin de pagination
     */
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

    //    Nombre d'absence total
    public function getAbsTotal(): int
    {
        if ($this->absTotal !== null) {
            return $this->absTotal;
        }

        global $connection;
        $request = $connection->prepare("SELECT COUNT(*) FROM absence WHERE idStudent = ?");
        $request->bindParam(1, $this->idAccount);
        $request->execute();
        $result = $request->fetch();
        $this->absTotal = $result[0];
        return $result[0];
    }

    //    Absences pouvant encore être justifiées (allowedJustification = true)
    public function getAbsCanBeJustified(): int {
        if ($this->absCanBeJustified !== null) {
            return $this->absCanBeJustified;
        }

        global $connection;

        $query = "SELECT COUNT(*) FROM absence WHERE idStudent = ? AND allowedJustification = true";

        $request = $connection->prepare($query);
        $request->bindParam(1, $this->idAccount);
        $request->execute();
        $result = $request->fetch();
        $this->absCanBeJustified = $result[0];
        return $result[0];
    }

    //    Absences à l'état "Non-justifié"
    public function getAbsNotJustified(): int
    {
        if ($this->absNotJustified !== null) {
            return $this->absNotJustified;
        }

        global $connection;
        $request = $connection->prepare("SELECT COUNT(*) FROM absence WHERE idStudent = ? AND currentState = 'NotJustified'");
        $request->bindParam(1, $this->idAccount);
        $request->execute();
        $result = $request->fetch();
        $this->absNotJustified = $result[0];
        return $result[0];
    }

    //    Absences à l'état "Refusé"
    public function getAbsRefused(): int
    {
        if ($this->absRefused !== null) {
            return $this->absRefused;
        }

        global $connection;
        $request = $connection->prepare("SELECT COUNT(*) FROM absence WHERE idStudent = ? AND currentState = 'Refused'");
        $request->bindParam(1, $this->idAccount);
        $request->execute();
        $result = $request->fetch();
        $this->absRefused = $result[0];
        return $result[0];

    }

    /*
     * Nombre de demi-journées d'absence (matin < 12:30 ; après-midi ≥ 12:30)
     * Peut ainsi compter deux absences le même jour, mais pas plus
     */
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
        $query->bindValue(':idstudent', $this->idAccount, PDO::PARAM_INT);
        $query->execute();

        $this->halfdaysAbsences = (int)$query->fetchColumn();

        return $this->halfdaysAbsences;
    }

    /*
     * Points de malus incluant les états Pending/NotJustified/Refused.
     * 0 si < seuil, sinon demi-journées * taux
     */
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
                and currentState in ('Refused','NotJustified','Pending')
            group by a.idStudent, day
        ),
        view_afternoon_absences as 
        (
            select a.idStudent, cast(time as date) as day
            from absence a
            where cast(a.time as time) >= time '12:30' 
                and currentState in ('Refused','NotJustified','Pending')
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
        $query->bindValue(':idstudent', $this->idAccount, PDO::PARAM_INT);
        $query->execute();

        $halfdays = (int)$query->fetchColumn();

        $this->malusPoints = ($halfdays >= self::MALUS_TRESSHOLD) ? $halfdays * self::MALUS_POINTS : 0.0;

        return $this->malusPoints;
    }

    /*
     * Points de malus incluant les états NotJustified/Refused,
     * Utile pour afficher l'impacte de la validation des absences en attente
     */
    public function getMalusPointsWithoutPending(): float
    {
        if ($this->malusPointsWithoutPending !== null) {
            return $this->malusPointsWithoutPending;
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
        $query->bindValue(':idstudent', $this->idAccount, PDO::PARAM_INT);
        $query->execute();

        $halfdays = (int)$query->fetchColumn();

        $this->malusPointsWithoutPending = ($halfdays >= self::MALUS_TRESSHOLD) ? $halfdays * self::MALUS_POINTS : 0.0;

        return $this->malusPointsWithoutPending;
    }

    public function getPenalizingAbsence(): int
    {
        global $connection;
        $request = $connection->prepare("SELECT COUNT(*) FROM absence WHERE idStudent = ? and currentState in ('Refused','NotJustified', 'Pending')");
        $request->bindParam(1, $this->idAccount);
        $request->execute();
        $result = $request->fetch();
        $this->absNotJustified = $result[0];
        return $result[0];
    }

    public static function getStudentByIdAccount($id): Student {
        global $connection;

        $query = "SELECT * FROM Account 
                  JOIN Student USING(idAccount) 
                  JOIN GroupStudent USING(idGroupStudent)           
                  WHERE idAccount = ?";
        $req = $connection->prepare($query);
        $req->execute(array($id));

        $res = $req->fetch();

        return new Student(
            $res['idaccount'],
            $res['lastname'],
            $res['firstname'],
            $res['email'],
            AccountType::from($res['accounttype']),
            $res['studentnumber'],
            new GroupStudent($res['idgroupstudent'], $res['label'])
        );
    }
}
