<?php
namespace Uphf\GestionAbsence\Model\Account;

use Uphf\GestionAbsence\Model\Connection;
use Uphf\GestionAbsence\Model\Filter\FilterStudent;
use PDO;

/**
 * Classe Student, basé sur la BDD
 */
class Student extends Account {
    // Attribut de base de la classe
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

    // Array de la classe
    private array $absences = [];
    private array $justifications = [];

    // Constante de la classe : évitement des valeurs hasardeuses
    private const MALUS_THRESHOLD = 5; // Utilisé pour la limite d'affichage du malus
    private const MALUS_POINTS = 0.1; // Utilisé pour la multiplication du malus


    public function __construct($idAccount, $lastName, $firstName, $email, $accountType, $studentNumber, $groupStudent)
    {
        parent::__construct($idAccount, $lastName, $firstName, $email, $accountType);
        $this->studentNumber = $studentNumber;
        $this->groupStudent = $groupStudent;
    }

    /**
     * Serialization
     * Utilisé quand on met un objet dans $_SESSION
     * @return array
     */
    public function __serialize(): array {
        return parent::__serialize() + ['studentNumber' => $this->studentNumber, 'groupStudent' => $this->groupStudent];
    }

    /**
     * Unserialization
     * Utilisé par session_start pour récupérer un objet stocké dans la session
     * @param array $data
     * @return void
     */
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

    /**
     * Récupérer le nombre d'absences total d'un étudiant
     * @return int
     */
    public function getAbsTotal(): int
    {
        if ($this->absTotal !== null) {
            return $this->absTotal;
        }

        $connection = Connection::getInstance();
        $request = $connection->prepare("SELECT COUNT(*) FROM absence WHERE idStudent = ?");
        $request->bindParam(1, $this->idAccount);
        $request->execute();
        $result = $request->fetch();
        $this->absTotal = $result[0];
        return $result[0];
    }

    /**
     * Récupérer le nombre d'absences pouvant être justifiées (allowedJustification = true)
     * @return int
     */
    public function getAbsCanBeJustified(): int {
        if ($this->absCanBeJustified !== null) {
            return $this->absCanBeJustified;
        }

        $connection = Connection::getInstance();

        $query = "SELECT COUNT(*) FROM absence WHERE idStudent = ? AND allowedJustification = true";

        $request = $connection->prepare($query);
        $request->bindParam(1, $this->idAccount);
        $request->execute();
        $result = $request->fetch();
        $this->absCanBeJustified = $result[0];
        return $result[0];
    }

    /**
     * Récupérer le nombre d'absences avec l'état "Non-justifié"
     * @return int
     */
    public function getAbsNotJustified(): int
    {
        if ($this->absNotJustified !== null) {
            return $this->absNotJustified;
        }

        $connection = Connection::getInstance();
        $request = $connection->prepare("SELECT COUNT(*) FROM absence WHERE idStudent = ? AND currentState = 'NotJustified'");
        $request->bindParam(1, $this->idAccount);
        $request->execute();
        $result = $request->fetch();
        $this->absNotJustified = $result[0];
        return $result[0];
    }

    /**
     * Récupérer le nombre d'absences avec l'état "Refusé"
     * @return int
     */
    public function getAbsRefused(): int
    {
        if ($this->absRefused !== null) {
            return $this->absRefused;
        }

        $connection = Connection::getInstance();
        $request = $connection->prepare("SELECT COUNT(*) FROM absence WHERE idStudent = ? AND currentState = 'Refused'");
        $request->bindParam(1, $this->idAccount);
        $request->execute();
        $result = $request->fetch();
        $this->absRefused = $result[0];
        return $result[0];

    }

    /**
     * Récupérer le nombre de demi-journées d'absence (matin < 12h30 ; après-midi ≥ 12h30)
     *
     * Peut ainsi comptabiliser deux demi-journées d'absence le même jour.
     * @return int
     */
    public function getHalfdaysAbsences(): int
    {
        if ($this->halfdaysAbsences !== null) {
            return $this->halfdaysAbsences;
        }

        $connection = Connection::getInstance();

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

    /**
     * Récupérer le malus cosé par les demi-journées d'absence.
     *
     * Le malus est calculé sur les demi-journées ayant des absences avec les états suivants:
     * - Pending
     * - NotJustified
     * - Refused
     *
     * 0 si malus < seuil, sinon demiJournees * taux
     * @return float
     */
    public function getMalusPoints(): float
    {
        if ($this->malusPoints !== null) {
            return $this->malusPoints;
        }

        $connection = Connection::getInstance();

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

        $this->malusPoints = ($halfdays >= self::MALUS_THRESHOLD) ? $halfdays * self::MALUS_POINTS : 0.0;

        return $this->malusPoints;
    }

    /**
     * Récupérer le malus cosé par les demi-journées d'absence.
     *
     * Le malus est calculé sur les mêmes états que la méthode getMalusPoints()
     * en excluant l'état Pending.
     *
     * Utilisé pour afficher l'impacte de la validation des absences en attente
     *
     * @return float
     */
    public function getMalusPointsWithoutPending(): float
    {
        if ($this->malusPointsWithoutPending !== null) {
            return $this->malusPointsWithoutPending;
        }

        $connection = Connection::getInstance();

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

        $this->malusPointsWithoutPending = ($halfdays >= self::MALUS_THRESHOLD) ? $halfdays * self::MALUS_POINTS : 0.0;

        return $this->malusPointsWithoutPending;
    }

    /**
     * Récupérer le nombre d'absences "Pénalisante"
     *
     * Cela inclut toutes les absences avec l'état suivant:
     * - Pending
     * - NotJustified
     * - Refused
     *
     * @return int
     */
    public function getPenalizingAbsence(): int
    {
        $connection = Connection::getInstance();
        $request = $connection->prepare("SELECT COUNT(*) FROM absence WHERE idStudent = ? and currentState in ('Refused','NotJustified', 'Pending')");
        $request->bindParam(1, $this->idAccount);
        $request->execute();
        $result = $request->fetch();
        $this->absNotJustified = $result[0];
        return $result[0];
    }

    /**
     * Récupérer dans la BDD un étudiant par son ID
     *
     * @param $id
     * @return Student | null
     */
    public static function getStudentByIdAccount(int $id): Student | null {
        $connection = Connection::getInstance();

        $query = "SELECT * FROM Account 
                  JOIN Student USING(idAccount) 
                  JOIN GroupStudent USING(idGroupStudent)           
                  WHERE idAccount = ?";
        $req = $connection->prepare($query);
        $req->execute(array($id));

        $res = $req->fetch();

        if($res)
        {
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
        else
        {
            return null;
        }
    }

    /**
     * Récupérer depuis la BDD les étudiants correspondant au filtre
     *
     * S'il n'y a pas de filtre de recherche, alors le trie est par ordre croissant des noms / prénoms
     * Sinon, le trie est effectué par pertinence de la chaine de recherche.
     *
     * La recherche s'effectue par token, et récupère les étudiants qui match avec
     * l'ensemble des tokens. Un token est un "mot" dans la phrase de recherche.
     *
     * Il y a match entre le token est l'étudiant si le token a une similarité de 0.2 avec le nom ou le prénom normalisé.
     *
     * @param FilterStudent $filter
     * @return Student[]
     */
    public static function getAllStudents(FilterStudent $filter): array {
        $connection = Connection::getInstance();

        $parameters = array(); // valeurs à binder sur la requête préparée
        $where = array(); // conditions SQL

        $column = "Account.*, Student.*, GroupStudent.label AS GroupStudent";

        // S'il y a une valeur de recherche, on ajoute la valeur qui représente la pertinence du résultat.
        if($filter->getSearch() != null) {
            $column .= ", greatest (
                similarity(search_lastname, unaccent(lower(:search))),
                similarity(search_firstname, unaccent(lower(:search))),
                similarity(search_lastname || ' ' || search_firstname, unaccent(lower(:search)))
            ) AS sim";

            $parameters["search"] = $filter->getSearch();
        }

        $query = "SELECT $column FROM Account
                  JOIN Student USING(idAccount)
                  JOIN GroupStudent USING(idGroupStudent)";

        if($filter->getGroupStudent() != null) {
            $where[] = "idGroupStudent = :idGroupStudent";
            $parameters['idGroupStudent'] = $filter->getGroupStudent();
        }
        if($filter->getSearch() != null) {
            $connection->exec("SET pg_trgm.similarity_threshold = 0.2");

            $tokens = explode(' ', $filter->getSearch());

            $i = 0;
            foreach($tokens as $token) {
                if($token == '') { continue; } // Alléger la requête dans le cas de multiple espace consécutif

                // Si le token a une longeur de plus de 4, utilisation de l'opérateur de similarité '%'
                if(mb_strlen($token) > 4) {
                    $where[] = "(search_lastname % unaccent(lower(:token$i)) OR search_firstname % unaccent(lower(:token$i)))";
                }
                // Sinon, utilisation de LIKE
                else {
                    $where[] = "(
                        search_lastname LIKE unaccent(lower(:token$i)) || '%' OR
                        search_firstname LIKE unaccent(lower(:token$i)) || '%' OR
                        search_lastname % unaccent(lower(:token$i)) OR
                        search_firstname % unaccent(lower(:token$i))
                    )";
                }

                $parameters["token".$i++] = $token;
            }
        }

        // Construction finale de la requête
        if (!empty($where)) {
            $query .= " WHERE " . implode(" AND ", $where);
        }

        if($filter->getSearch() != null) {
            $query .= " ORDER BY sim DESC";
        }
        else {
            $query .= " ORDER BY lastname, firstname";
        }

        echo $query.'<br>';

        // Préparation + binding des paramètres
        $req = $connection->prepare($query);
        foreach ($parameters as $key => $value) {
            $req->bindValue(':'.$key, $value);
        }
        $req->execute();
        $res = $req->fetchAll();

        $students = [];

        foreach ($res as $r) {
            $students[] = new Student(
                $r['idaccount'],
                $r['lastname'],
                $r['firstname'],
                $r['email'],
                AccountType::from($r['accounttype']),
                $r['studentnumber'],
                new GroupStudent(
                    $r['idgroupstudent'],
                    $r['groupstudent']
                )
            );
        }

        return $students;
    }
}