<?php

namespace Uphf\GestionAbsence\Model\DB\Select\SelectBuilder;

use Uphf\GestionAbsence\Model\DB\Connection;
use Uphf\GestionAbsence\Model\Entity\Justification\Justification;
use Uphf\GestionAbsence\Model\Entity\Justification\StateJustif;
use Uphf\GestionAbsence\Model\Hydrator\JustificationHydrator;
use BadMethodCallException;
use PDO;

/**
 * Builder permettant de construire facilement une requête pour obtenir une liste de justificatifs
 *
 * Pour une instance du builder, chaque méthode ne peut être appelée qu'une fois.
 */
class JustificationSelectBuilder {
    private array $where = [];
    private array $parameters = [];
    private array $orderByColumns;
    private SortOrder $order;
    private array $flags = [];

    /**
     * Filtrer les justificatifs pour un étudiant
     *
     * @param int $idStudent
     * @return $this
     * @throws BadMethodCallException Si c'est le second appel de la méthode
     */
    public function idStudent(int $idStudent): JustificationSelectBuilder {
        if(isset($this->flags['idStudent'])) { throw new BadMethodCallException("Second appel de la méthode 'idStudent()'."); }
        $this->flags['idStudent'] = true;

        $this->where[] = "idstudent = :studentid";
        $this->parameters['studentid'] = [$idStudent, PDO::PARAM_INT];

        return $this;
    }

    /**
     * Filtrer les justificatifs postérieurs à une date donnée
     *
     * @param string $date
     * @return $this
     * @throws BadMethodCallException Si c'est le second appel de la méthode
     */
    public function dateStart(string $date): JustificationSelectBuilder {
        if(isset($this->flags['dateStart'])) { throw new BadMethodCallException("Second appel de la méthode 'dateStart()'."); }
        $this->flags['dateStart'] = true;

        $this->where[] = "endDate >= :startDate";
        $this->parameters["startDate"] = [$date, PDO::PARAM_STR];

        return $this;
    }

    /**
     * Filtrer les justificatifs antérieurs à une date donnée (incluse)
     *
     * @param string $date
     * @return $this
     * @throws BadMethodCallException Si c'est le second appel de la méthode
     */
    public function dateEnd(string $date): JustificationSelectBuilder {
        if(isset($this->flags['dateEnd'])) { throw new BadMethodCallException("Second appel de la méthode 'dateEnd()'."); }
        $this->flags['dateEnd'] = true;

        $this->where[] = "startdate <= :endDate";
        $this->parameters["endDate"] = [$date, PDO::PARAM_STR];

        return $this;
    }

    /**
     * Filtrer les justificatifs pour un état
     *
     * @param StateJustif $state
     * @return $this
     * @throws BadMethodCallException Si c'est le second appel de la méthode
     */
    public function state(StateJustif $state): JustificationSelectBuilder {
        if(isset($this->flags['state'])) { throw new BadMethodCallException("Second appel de la méthode 'state()'."); }
        $this->flags['state'] = true;

        $this->where[] = "j.currentState = :currentState";
        $this->parameters['currentState'] = [$state->value, PDO::PARAM_STR];

        return $this;
    }

    /**
     * Filtrer les justificatifs avec au moins une absence lors d'examen
     *
     * @return $this
     * @throws BadMethodCallException Si c'est le second appel de la méthode
     */
    public function examen(): JustificationSelectBuilder {
        if(isset($this->flags['examen'])) { throw new BadMethodCallException("Second appel de la méthode 'examen()'."); }
        $this->flags['examen'] = true;

        $this->where[] = "a.examen";

        return $this;
    }

    /**
     * Permet de définir les colonnes et l'ordre pour la clause ORDER BY de la requête
     *
     * IMPORTANT: Ne jamais passer de données fournies par l'utilisateur à \$columns
     *
     * @param array $columns
     * @param SortOrder $order
     * @return $this
     * @throws BadMethodCallException Si c'est le second appel de la méthode
     */
    public function orderBy(array $columns, SortOrder $order): JustificationSelectBuilder {
        if(isset($this->flags['orderBy'])) { throw new BadMethodCallException("Second appel de la méthode 'orderBy()'."); }
        $this->flags['orderBy'] = true;

        $this->orderByColumns = $columns;
        $this->order = $order;

        return $this;
    }

    /**
     * Construis et exécute la requête finale
     *
     * @param bool $debug Si sur true, affiche la requête construite
     * @return Justification[]
     */
    public function execute(bool $debug = false): array {
        $conn = Connection::getInstance();

        $query = "SELECT DISTINCT j.*, s.*
                  FROM justification j
                  JOIN absenceJustification aj USING(idJustification)
                  JOIN absence a USING(idStudent, time)
                  JOIN studentAccount s ON s.studentid = a.idstudent";

        if(!empty($this->where)) {
            $query .= " WHERE " . implode(" AND ", $this->where);
        }

        if(isset($this->orderByColumns)) {
            $query .= " ORDER BY " . implode(", ", $this->orderByColumns) . " " . $this->order->value;
        }

        if($debug) {
            echo "<pre>$query</pre>";
            echo "<pre>"; var_export($this->parameters); echo "</pre>";
        }

        $sql = $conn->prepare($query);

        foreach($this->parameters as $key => [$value, $type]) {
            $sql->bindValue(":$key", $value, $type);
        }

        $sql->execute();
        $rows = $sql->fetchAll(PDO::FETCH_ASSOC);

        $justifications = [];

        foreach($rows as $raw) {
            $justifications[] = JustificationHydrator::unserializeJustification($raw);
        }

        return $justifications;
    }
}
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . "/../../../../../vendor/autoload.php";

function showJustification($justifications) {
    echo "<pre>Nb de justificatifs: " . count($justifications) . "</pre>";

    foreach($justifications as $j) {
        echo "<pre>";
        echo "idJustification: " . $j->getIdJustification() . " ";
        echo "</pre>";
    }
}

//$justifications = new JustificationSelectBuilder()->execute(true);
//$justifications = new JustificationSelectBuilder()->idStudent(1)->execute(true);
//$justifications = new JustificationSelectBuilder()->dateStart("2025-11-11")->execute(true);
//$justifications = new JustificationSelectBuilder()->dateEnd("2025-11-10")->execute(true);
//$justifications = new JustificationSelectBuilder()->state(StateJustif::NotProcessed)->execute(true);
//$justifications = new JustificationSelectBuilder()->examen()->execute(true);
//$justifications = new JustificationSelectBuilder()->orderBy(['idJustification'], SortOrder::ASC)->execute(true);
//$justifications = new JustificationSelectBuilder()->examen()->orderBy(['idJustification'], SortOrder::ASC)->execute(true);
//$justifications = new JustificationSelectBuilder()->idStudent(2)->examen()->execute(true);

// Throw Exception
//$justifications = new JustificationSelectBuilder()->idStudent(1)->idStudent(1)->execute(true);
//$justifications = new JustificationSelectBuilder()->dateStart("2025-11-11")->dateStart("2025-11-11")->execute(true);
//$justifications = new JustificationSelectBuilder()->dateEnd("2025-11-11")->dateEnd("2025-11-11")->execute(true);
//$justifications = new JustificationSelectBuilder()->state(StateJustif::Processed)->state(StateJustif::Processed)->state(true);
//$justifications = new JustificationSelectBuilder()->examen()->examen()->execute(true);
//$justifications = new JustificationSelectBuilder()->orderBy(['idJustification'], SortOrder::ASC)->orderBy(['idJustification'], SortOrder::ASC)->execute(true);

showJustification($justifications);
*/