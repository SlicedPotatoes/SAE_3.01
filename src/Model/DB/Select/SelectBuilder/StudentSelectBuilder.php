<?php

namespace Uphf\GestionAbsence\Model\DB\Select\SelectBuilder;

use Uphf\GestionAbsence\Model\Entity\Account\Student;
use Uphf\GestionAbsence\Model\DB\Connection;
use PDO;
use BadMethodCallException;
use Uphf\GestionAbsence\Model\Hydrator\AccountHydrator;

/**
 * Builder permettant de construire facilement une requête pour obtenir une liste d'étudiants.
 *
 * L'appel à une seule des deux méthodes principales est obligatoire (searchBar ou allStudent).
 */
class StudentSelectBuilder {
    private string $query;
    private array $where = [];
    private array $parameters = [];
    private array $flags = [];

    /**
     * Filtrer par similarité avec searchValue
     *
     * @param string $searchValue
     * @return $this
     * @throws BadMethodCallException Si c'est le second appel à une méthode principale (searchBar ou allStudent).
     */
    public function searchBar(string $searchValue): StudentSelectBuilder {
        if(isset($this->flags['main'])) { throw new BadMethodCallException("Un seul appel à une méthode principale (searchBar ou allStudent)"); }
        $this->flags['main'] = true;

        $this->query = "SELECT *, greatest (
            similarity(search_lastname, unaccent(lower(:search))),
            similarity(search_firstname, unaccent(lower(:search))),
            similarity(search_lastname || ' ' || search_firstname, unaccent(lower(:search)))
        ) AS sim
        FROM StudentAccount";

        $this->parameters["search"] = [$searchValue, PDO::PARAM_STR];

        $tokens = explode(' ', $searchValue);

        $i = 0;
        foreach ($tokens as $token) {
            if($token == '') { continue; } // Alléger la requête dans le cas de multiple espace consécutif

            // Si le token a une longueur de plus de 4, utilisation de l'opérateur de similarité '%'
            if(mb_strlen($token) > 4) {
                $this->where[] = "(search_lastname % unaccent(lower(:token$i)) OR search_firstname % unaccent(lower(:token$i)))";
            }
            // Sinon, utilisation de LIKE
            else {
                $this->where[] = "(
                   search_lastname LIKE '%' || unaccent(lower(:token$i)) || '%' OR
                   search_firstname LIKE '%' || unaccent(lower(:token$i)) || '%' OR
                   search_lastname % unaccent(lower(:token$i)) OR
                   search_firstname % unaccent(lower(:token$i))
                )";
            }

            $this->parameters["token".$i++] = [$token, PDO::PARAM_STR];
        }

        return $this;
    }

    /**
     * Requête sur l'ensemble des étudiants
     *
     * @return $this
     * @throws BadMethodCallException Si c'est le second appel à une méthode principale (searchBar ou allStudent).
     */
    public function allStudent(): StudentSelectBuilder {
        if(isset($this->flags['main'])) { throw new BadMethodCallException("Un seul appel à une méthode principale (searchBar ou allStudent)"); }
        $this->flags['main'] = true;

        $this->query = "SELECT * FROM studentAccount";

        return $this;
    }

    /**
     * Filtrer les étudiants par GroupStudent
     *
     * @param int $idGroupStudent
     * @return $this
     * @throws BadMethodCallException Si c'est le second appel de la méthode
     */
    public function groupStudent(int $idGroupStudent): StudentSelectBuilder {
        if(isset($this->flags['groupStudent'])) { throw new BadMethodCallException("Second appel de la méthode 'groupStudent()'."); }
        $this->flags['groupStudent'] = true;

        $this->where[] = "groupid = :idGroupStudent";
        $this->parameters ['idGroupStudent'] = [$idGroupStudent, PDO::PARAM_INT];

        return $this;
    }

    /**
     * Construis et exécute la requête finale
     *
     * @param bool $debug Si sur true, affiche la requête construite
     * @return Student[]
     */
    public function execute(bool $debug = false): array {
        if(!isset($this->flags['main'])) {
            throw new BadMethodCallException("L'exécution d'une des méthodes principales est obligatoire (searchBar ou allStudent)");
        }

        $conn = Connection::getInstance();

        if(!empty($this->where)) {
            $this->query .= " WHERE " . implode(" AND ", $this->where);
        }

        if(isset($this->parameters['search'])) {
            $this->query .= " ORDER BY sim DESC";

            $conn->exec("SET pg_trgm.similarity_threshold = 0.2");
        }
        else {
            $this->query .= " ORDER BY lastname, firstname";
        }

        if($debug) {
            echo "<pre>$this->query</pre>";
            echo "<pre>"; var_export($this->parameters); echo "</pre>";
        }

        $sql = $conn->prepare($this->query);
        foreach($this->parameters as $key => [$value, $type]) {
            $sql->bindValue(":$key", $value, $type);
        }
        $sql->execute();

        $rows = $sql->fetchAll(PDO::FETCH_ASSOC);

        $students = [];

        foreach ($rows as $raw) {
            $students[] = AccountHydrator::unserializeStudent($raw);
        }

        return $students;
    }
}

/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . "/../../../../../vendor/autoload.php";

function showStudent($students) {
    echo "<pre>Nb Student: " . count($students) . "</pre>";

    foreach($students as $student) {
        echo "<pre>";
        echo "idAccount: " . $student->getIdAccount() . " ";
        echo "Group: " . $student->getGroupStudent()->getLabel() . " ";
        echo $student->getLastName() . " " . $student->getFirstName();
        echo "</pre>";
    }
}

//$students = new StudentSelectBuilder()->allStudent()->execute(true);
//$students = new StudentSelectBuilder()->allStudent()->groupStudent(-2)->execute(true);
//$students = new StudentSelectBuilder()->searchBar("van stinquiste Dim")->execute(true);
$students = new StudentSelectBuilder()->searchBar("matthis")->groupStudent(-1)->execute(true);

// Throw Exception
//$students = new StudentSelectBuilder()->allStudent()->allStudent()->execute(true);
//$students = new StudentSelectBuilder()->allStudent()->searchBar("")->execute(true);
//$students = new StudentSelectBuilder()->execute(true);
//$students = new StudentSelectBuilder()->allStudent()->groupStudent(-1)->groupStudent(-1)->execute(true);

showStudent($students);
*/