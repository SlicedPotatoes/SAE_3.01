<?php

namespace Uphf\GestionAbsence\Model\DB\Select\SelectBuilder;

use Uphf\GestionAbsence\Model\Hydrator\AbsenceHydrator;
use Uphf\GestionAbsence\Model\DB\Connection;
use Uphf\GestionAbsence\Model\Entity\Absence\Absence;
use Uphf\GestionAbsence\Model\Entity\Absence\StateAbs;
use PDO;
use BadMethodCallException;

/**
 * Builder permettant de construire facilement une requête pour obtenir une liste d'absence
 *
 * Pour une instance du builder, chaque méthode ne peut être appelée qu'une fois.
 */
class AbsenceSelectBuilder {
    private array $where = [];
    private array $parameters = [];
    private array $orderByColumns;
    private SortOrder $order;
    private array $flags = [];

    /**
     * Filtrer les absences pour un étudiant
     *
     * @param int $idStudent
     * @return $this
     * @throws BadMethodCallException Si c'est le second appel de la méthode
     */
    public function idStudent(int $idStudent): AbsenceSelectBuilder {
        if(isset($this->flags['idStudent'])) { throw new BadMethodCallException("Second appel de la méthode 'idStudent()'."); }
        $this->flags['idStudent'] = true;

        $this->where[] = "idstudent = :studentId";
        $this->parameters["studentId"] = [$idStudent, PDO::PARAM_INT];
        return $this;
    }

    /**
     * Filtrer les absences postérieures à une date donnée
     *
     * @param string $date
     * @return $this
     * @throws BadMethodCallException Si c'est le second appel de la méthode
     */
    public function dateStart(string $date): AbsenceSelectBuilder {
        if(isset($this->flags['dateStart'])) { throw new BadMethodCallException("Second appel de la méthode 'dateStart()'."); }
        $this->flags['dateStart'] = true;

        $this->where[] = "time >= :startDate";
        $this->parameters["startDate"] = [$date, PDO::PARAM_STR];
        return $this;
    }

    /**
     * Filtrer les absences antérieures à une date donnée (incluse)
     *
     * @param string $date
     * @return $this
     * @throws BadMethodCallException Si c'est le second appel de la méthode
     */
    public function dateEnd(string $date): AbsenceSelectBuilder {
        if(isset($this->flags['dateEnd'])) { throw new BadMethodCallException("Second appel de la méthode 'dateEnd()'."); }
        $this->flags['dateEnd'] = true;

        $this->where[] = "time <= cast(:endDate as date) + interval '1 day'";
        $this->parameters["endDate"] = [$date, PDO::PARAM_STR];
        return $this;
    }

    /**
     * Filtrer les absences pour un état
     *
     * @param StateAbs $state
     * @return $this
     * @throws BadMethodCallException Si c'est le second appel de la méthode
     */
    public function state(StateAbs $state): AbsenceSelectBuilder {
        if(isset($this->flags['state'])) { throw new BadMethodCallException("Second appel de la méthode 'state()'."); }
        $this->flags['state'] = true;

        $this->where[] = "currentState = :state";
        $this->parameters["state"] = [$state->value, PDO::PARAM_STR];
        return $this;
    }

    /**
     * Filtrer les absences avec ou sans examen
     *
     * @param bool $examen
     * @return $this
     * @throws BadMethodCallException Si c'est le second appel de la méthode
     */
    public function examen(bool $examen): AbsenceSelectBuilder {
        if(isset($this->flags['examen'])) { throw new BadMethodCallException("Second appel de la méthode 'examen()'."); }
        $this->flags['examen'] = true;

        $this->where[] = ($examen ? "" : "NOT ") . "examen";
        return $this;
    }

    /**
     * Filtrer les absences pouvant ou non être justifiées
     *
     * Quand on parle d'absence ne pouvant pas être justifiée, on fait référence à celle etant "Refusé" ou "Non justifiée".
     * (Celle "Validée" sont d'office non justifiable).
     *
     * @param bool $lock
     * @return $this
     * @throws BadMethodCallException Si c'est le second appel de la méthode
     */
    public function lock(bool $lock): AbsenceSelectBuilder {
        if(isset($this->flags['lock'])) { throw new BadMethodCallException("Second appel de la méthode 'lock()'."); }
        $this->flags['lock'] = true;

        $this->where[] = $lock ?
            "NOT allowedJustification AND (currentState = 'Refused' OR currentState = 'NotJustified')"
            : "allowedJustification";
        return $this;
    }

    /**
     * Permet de définir les colonnes et l'ordre pour la clause ORDER BY de la requête
     *
     * IMPORTANT: Ne jamais passer de données fournies par l'utilisateur à \$columbs
     *
     * @param array $columns
     * @param SortOrder $order
     * @return $this
     * @throws BadMethodCallException Si c'est le second appel de la méthode
     */
    public function orderBy(array $columns, SortOrder $order): AbsenceSelectBuilder {
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
     * @return Absence[]
     */
    public function execute(bool $debug = false): array {
        $conn = Connection::getInstance();

        $query = "SELECT 
                    s.studentid,
                    s.lastname AS studentLastName,
                    s.firstname AS studentFirstName,
                    s.email AS studentEmail,
                    s.accounttype AS studentAccountType,
                    s.studentnumber,
                    s.groupid,
                    s.grouplabel,
                    a.*,
                    r.*,
                    t.idaccount AS teacherid,
                    t.lastname AS teacherLastName,
                    t.firstname AS teacherFirstName,
                    t.email AS teacherEmail,
                    t.accounttype AS teacherAccountType
                  FROM Absence a
                  JOIN Resource r USING(idResource)
                  LEFT JOIN Account t ON a.idTeacher = t.idAccount
                  JOIN StudentAccount s ON a.idStudent = s.studentid";

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
            $sql->bindValue(':'.$key, $value, $type);
        }

        $sql->execute();
        $rows = $sql->fetchAll(PDO::FETCH_ASSOC);

        $absences = [];

        foreach($rows as $r) {
            $absences[] = AbsenceHydrator::unserializeAbsence($r);
        }

        return $absences;
    }
}
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . "/../../../../../vendor/autoload.php";

function showAbs($abs) {
    echo "<pre>Nb d'absence: " . count($abs) . "</pre>";

    foreach($abs as $a) {
        echo "<pre>";
        echo "idAccount: " . $a->getIdAccount() . " ";
        echo "time: " . $a->getTime()->format("Y-m-d H:i:s") . " ";
        echo "examen: " . ($a->getExamen() ? 1 : 0) . " ";
        echo "AllowedJustification: " . ($a->getAllowedJustification() ? 1 : 0) . " ";
        echo "currentState: " . $a->getCurrentState()->value . " ";
        echo "</pre>";
    }
}


//$abs = new AbsenceSelectBuilder()->execute(true);
//$abs = new AbsenceSelectBuilder()->idStudent(1)->execute(true);
//$abs = new AbsenceSelectBuilder()->dateStart('2025-10-21')->execute(true);
//$abs = new AbsenceSelectBuilder()->dateEnd('2025-10-21')->execute(true);
//$abs = new AbsenceSelectBuilder()->state(StateAbs::Validated)->execute(true);
//$abs = new AbsenceSelectBuilder()->examen(true)->execute(true);
//$abs = new AbsenceSelectBuilder()->examen(false)->execute(true);
//$abs = new AbsenceSelectBuilder()->lock(false)->execute(true);
//$abs = new AbsenceSelectBuilder()->lock(true)->execute(true);
//$abs = new AbsenceSelectBuilder()->dateStart('2025-10-21')->dateEnd('2025-10-21')->execute(true);

//$abs = new AbsenceSelectBuilder()->orderBy(['time', 'currentState'], SortOrder::DESC)->execute(true);

// Throw Exception
//$abs = new AbsenceSelectBuilder()->idStudent(1)->idStudent(1)->execute(true);
//$abs = new AbsenceSelectBuilder()->dateStart('2025-10-21')->dateStart('2025-10-21')->execute();
//$abs = new AbsenceSelectBuilder()->dateEnd('2025-10-21')->dateEnd('2025-10-21')->execute();
//$abs = new AbsenceSelectBuilder()->state(StateAbs::Pending)->state(StateAbs::NotJustified)->execute();
//$abs = new AbsenceSelectBuilder()->examen(true)->examen(true)->execute();
//$abs = new AbsenceSelectBuilder()->lock(true)->lock(true)->execute();

showAbs($abs);
*/