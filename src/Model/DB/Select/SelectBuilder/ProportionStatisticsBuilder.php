<?php

namespace Uphf\GestionAbsence\Model\DB\Select\SelectBuilder;

use BadMethodCallException;
use PDO;
use Uphf\GestionAbsence\Model\DB\Connection;

/**
 * Builder permettant de construire facilement une requête pour obtenir les données des graphiques de proportions
 *
 * Pour une instance du builder, chaque méthode ne peut être appelée qu'une fois.
 */
class ProportionStatisticsBuilder {
    private ProportionStatisticsType $pst;
    private array $join = [];
    private array $where = [];
    private array $params = [];
    private array $flags = [];

    /**
     * Définir le type de données récupérer par la requête
     * Le type de requête est gérer par l'énumération
     *
     * @param ProportionStatisticsType $pst
     * @return $this
     */
    public function type(ProportionStatisticsType $pst): ProportionStatisticsBuilder {
        if(isset($this->flags['type'])) { throw new BadMethodCallException("Second appel de la méthode 'type()'."); }
        $this->flags['type'] = true;
        $this->pst = $pst;

        return $this;
    }

    /**
     * Filtrer les données pour un groupe précis
     *
     * @param string $gID
     * @return $this
     */
    public function group(string $gID): ProportionStatisticsBuilder {
        if(!isset($this->pst)) { throw new BadMethodCallException("Un type doit être spécifier avant d'appeler 'group()'."); }
        if(isset($this->flags['group'])) { throw new BadMethodCallException("Second appel de la méthode 'group()'."); }
        $this->flags['group'] = true;

        if($this->pst != ProportionStatisticsType::Group) {
            $this->join[] = "JOIN student s ON a.idstudent = s.idaccount";
        }

        $this->where[] = "s.idgroupstudent = :idgroupstudent";
        $this->params["idgroupstudent"] = [$gID, PDO::PARAM_INT];
        return $this;
    }

    /**
     * Considérer les données seulement pendent examen
     *
     * @return $this
     */
    public function examen(): ProportionStatisticsBuilder {
        if(isset($this->flags['examen'])) { throw new BadMethodCallException("Second appel de la méthode 'examen()'."); }
        $this->flags['examen'] = true;

        $this->where[] = "a.examen";
        return $this;
    }

    /**
     * Filtrer les donneés pour un étudiant
     *
     * @param int $idStudent
     * @return $this
     */
    public function idStudent(int $idStudent): ProportionStatisticsBuilder {
        if(isset($this->flags['idStudent'])) { throw new BadMethodCallException("Second appel de la méthode 'idStudent()'."); }
        $this->flags['idStudent'] = true;

        $this->where[] = "a.idstudent = :idstudent";
        $this->params['idstudent'] = [$idStudent, PDO::PARAM_INT];

        return $this;
    }

    /**
     * Construis et exécute la requête finale
     *
     * @param bool $debug Si sur true, affiche la requête construite
     * @return array
     */
    public function execute(bool $debug = false): array {
        if(!isset($this->pst)) { throw new BadMethodCallException("Un type doit être spécifier avant d'appeler 'execute()'."); }
        $conn = Connection::getInstance();

        $join = $this->join;
        $join[] = $this->pst->join();

        $query = 'SELECT COUNT(*) as value, ' . $this->pst->select() . ' as label
                  FROM absence a ' .
                  implode(' ', $join);

        if(!empty($this->where)) {
            $query .= ' WHERE ' . implode(" AND ", $this->where);
        }

        $query .= ' GROUP BY ' . $this->pst->groupby();

        if($debug) {
            echo "<pre>$query</pre>";
            echo "<pre>"; var_export($this->params); echo "</pre>";
        }

        $sql = $conn->prepare($query);

        foreach($this->params as $key => [$value, $type]) {
            $sql->bindValue(':'.$key, $value, $type);
        }

        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
}

/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . "/../../../../../vendor/autoload.php";

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__,5));
$dotenv->load();

$result = new ProportionStatisticsBuilder()->type(ProportionStatisticsType::TypeCourse)->execute(true);
//$result = new ProportionStatisticsBuilder()->type(ProportionStatisticsType::Teacher)->execute(true);
//$result = new ProportionStatisticsBuilder()->type(ProportionStatisticsType::Resource)->execute(true);
//$result = new ProportionStatisticsBuilder()->type(ProportionStatisticsType::Group)->execute(true);
//$result = new ProportionStatisticsBuilder()->type(ProportionStatisticsType::State)->execute(true);
//$result = new ProportionStatisticsBuilder()->type(ProportionStatisticsType::Examen)->execute(true);

//$result = new ProportionStatisticsBuilder()->type(ProportionStatisticsType::TypeCourse)->examen()->execute(true);
//$result = new ProportionStatisticsBuilder()->type(ProportionStatisticsType::Teacher)->examen()->execute(true);
//$result = new ProportionStatisticsBuilder()->type(ProportionStatisticsType::Resource)->examen()->execute(true);
//$result = new ProportionStatisticsBuilder()->type(ProportionStatisticsType::Group)->examen()->execute(true);
//$result = new ProportionStatisticsBuilder()->type(ProportionStatisticsType::State)->examen()->execute(true);
//$result = new ProportionStatisticsBuilder()->type(ProportionStatisticsType::Examen)->examen()->execute(true);

//$result = new ProportionStatisticsBuilder()->type(ProportionStatisticsType::TypeCourse)->group(-1)->execute(true);
//$result = new ProportionStatisticsBuilder()->type(ProportionStatisticsType::Teacher)->group(-1)->execute(true);
//$result = new ProportionStatisticsBuilder()->type(ProportionStatisticsType::Resource)->group(-1)->execute(true);
//$result = new ProportionStatisticsBuilder()->type(ProportionStatisticsType::Group)->group(-1)->execute(true);
//$result = new ProportionStatisticsBuilder()->type(ProportionStatisticsType::State)->group(-1)->execute(true);
//$result = new ProportionStatisticsBuilder()->type(ProportionStatisticsType::Examen)->group(-1)->execute(true);

//$result = new ProportionStatisticsBuilder()->type(ProportionStatisticsType::Teacher)->idStudent(2)->execute(true);
//$result = new ProportionStatisticsBuilder()->type(ProportionStatisticsType::Teacher)->idStudent(5)->execute(true);

//$result = new ProportionStatisticsBuilder()->type(ProportionStatisticsType::TypeCourse)->examen()->group(-1)->execute(true);
//$result = new ProportionStatisticsBuilder()->type(ProportionStatisticsType::Teacher)->examen()->group(-1)->execute(true);
//$result = new ProportionStatisticsBuilder()->type(ProportionStatisticsType::Resource)->examen()->group(-1)->execute(true);
//$result = new ProportionStatisticsBuilder()->type(ProportionStatisticsType::Group)->examen()->group(-1)->execute(true);
//$result = new ProportionStatisticsBuilder()->type(ProportionStatisticsType::State)->examen()->group(-1)->execute(true);
//$result = new ProportionStatisticsBuilder()->type(ProportionStatisticsType::Examen)->examen()->group(-1)->execute(true);

// Throw Exception
//$result = new ProportionStatisticsBuilder()->execute();
//$result = new ProportionStatisticsBuilder()->type(ProportionStatisticsType::TypeCourse)->type(ProportionStatisticsType::TypeCourse)->execute(true);
//$result = new ProportionStatisticsBuilder()->group(-1)->execute();
//$result = new ProportionStatisticsBuilder()->type(ProportionStatisticsType::TypeCourse)->group(-1)->group(-1)->execute();
//$result = new ProportionStatisticsBuilder()->type(ProportionStatisticsType::TypeCourse)->examen()->examen()->execute();
//$result = new ProportionStatisticsBuilder()->type(ProportionStatisticsType::TypeCourse)->idStudent(2)->idStudent(2)->execute();

foreach ($result as $r) {
    echo "<pre>"; var_export($r); echo "</pre>";
}
*/