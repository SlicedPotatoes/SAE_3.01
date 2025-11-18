<?php

namespace Uphf\GestionAbsence\Model\DB\Update\UpdateBuilder;

use Uphf\GestionAbsence\Model\DB\Connection;
use Uphf\GestionAbsence\Model\Entity\Absence\Absence;
use Uphf\GestionAbsence\Model\Entity\Absence\StateAbs;
use BadMethodCallException;
use PDO;

/**
 * Builder permettant d'update des absences
 *
 * Il a deux types d'update possible:
 * - L'état de l'absence
 * - Autorisé ou non la justification de l'absence.
 *
 * Avant l'appel d'une des deux méthodes de mise à jour, il faut avoir chargé au préalable une absence.
 */
class AbsenceUpdateBuilder {
    private static array $queryHelper = [
        StateAbs::Validated->value => ["currentState", StateAbs::Validated->value, PDO::PARAM_STR],
        StateAbs::Pending->value => ["currentState", StateAbs::Pending->value, PDO::PARAM_STR],
        StateAbs::NotJustified->value => ["currentState", StateAbs::NotJustified->value, PDO::PARAM_STR],
        StateAbs::Refused->value => ["currentState", StateAbs::Refused->value, PDO::PARAM_STR],
        "lock" => ["allowedJustification", false, PDO::PARAM_BOOL],
        "notLock" => ["allowedJustification", true, PDO::PARAM_BOOL]
    ];
    private Absence $currentAbsence;
    private array $set = [
        StateAbs::Validated->value => [],
        StateAbs::NotJustified->value => [],
        StateAbs::Refused->value => [],
        StateAbs::Pending->value => [],
        "lock" => [],
        "notLock" => []
    ];

    private array $flags = [];

    /**
     * Permet de charger une absence
     *
     * @param Absence $a
     * @return $this
     */
    public function loadAbsence(Absence $a): AbsenceUpdateBuilder {
        $this->currentAbsence = $a;
        $this->flags = [];

        return $this;
    }

    /**
     * Mettre à jour l'état de l'absence
     *
     * @param StateAbs $state
     * @return $this
     * @throws BadMethodCallException Si c'est le second appel de la méthode pour l'absence sélectionné
     * @throws BadMethodCallException Si aucune absence n'est chargé
     */
    public function state(StateAbs $state): AbsenceUpdateBuilder {
        if(!isset($this->currentAbsence)) { throw new BadMethodCallException("Aucune absence chargé"); }
        if(isset($this->flags['state'])) { throw new BadMethodCallException("Second appel de la méthode 'state() pour cet absence'."); }
        $this->flags['state'] = true;

        $this->set[$state->value][] = $this->currentAbsence;
        $this->currentAbsence->setState($state);

        return $this;
    }

    /**
     * Mettre à jour l'attribut "AllowedJustification"
     *
     * @param bool $value
     * @return $this
     * @throws BadMethodCallException Si c'est le second appel de la méthode pour l'absence sélectionné
     * @throws BadMethodCallException Si aucune absence n'est chargé
     */
    public function allowedJustification(bool $value): AbsenceUpdateBuilder{
        if(!isset($this->currentAbsence)) { throw new BadMethodCallException("Aucune absence chargé"); }
        if(isset($this->flags['allowedJustification'])) { throw new BadMethodCallException("Second appel de la méthode 'allowedJustification() pour cet absence'."); }
        $this->flags['allowedJustification'] = true;

        $this->set[$value ? 'notLock' : 'lock'][] = $this->currentAbsence;
        $this->currentAbsence->setAllowedJustification($value);

        return $this;
    }

    /**
     * Éxécute les différentes requêtes pour update les absences
     *
     * Au maximum six requêtes UPDATE seront effectué à la base de données, qu'importe la quantité d'absence à mettre à jour.
     *
     * @param bool $debug Si sur true, affiche la requête construite
     * @return void
     */
    public function execute(bool $debug = false): void {
        $conn = Connection::getInstance();

        foreach ($this->set as $key => $listAbs) {
            if(empty($listAbs)) { continue; }

            [$column, $value, $type] = self::$queryHelper[$key];

            $where = [];
            $parameters = ["value" => [$value, $type]];

            for($i = 0; $i < count($listAbs); $i++) {
                $where[] = "(idStudent = :idStudent$i AND time = :time$i)";
                $parameters["idStudent$i"] = [$listAbs[$i]->getIdAccount(), PDO::PARAM_INT];
                $parameters["time$i"] = [$listAbs[$i]->getTime()->format("Y-m-d H:i:s"), PDO::PARAM_STR];
            }

            $query = "UPDATE Absence
                      SET $column = :value
                      WHERE " . implode(" OR ", $where);

            if($debug) {
                echo "<pre>$query</pre>";
                echo "<pre>"; var_export($parameters); echo "</pre>";
            }

            $sql = $conn->prepare($query);

            foreach ($parameters as $keyP => [$valueP, $type]) {
                $sql->bindValue(":$keyP", $valueP, $type);
            }

            $sql->execute();
        }
    }
}
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . "/../../../../../vendor/autoload.php";

use Uphf\GestionAbsence\Model\DB\Select\SelectBuilder\AbsenceSelectBuilder;
use Uphf\GestionAbsence\Model\DB\Select\SelectBuilder\SortOrder;

$absences = new AbsenceSelectBuilder()->dateStart('2025-10-21')->dateEnd('2025-10-21')->orderBy(["idStudent", "time"], SortOrder::ASC)->execute();

echo count($absences);

foreach ($absences as $abs) {
    echo "<pre>" . $abs->getIdAccount() . " " . $abs->getTime()->format("Y-m-d H:i:s") . "</pre>";
}

$state1 = StateAbs::Pending;
$state2 = StateAbs::Validated;
$value = false;

new AbsenceUpdateBuilder()->loadAbsence($absences[0])->state($state1)->allowedJustification($value )
                          ->loadAbsence($absences[1])->state($state1)->allowedJustification(!$value )
                          ->loadAbsence($absences[2])->state($state2)->allowedJustification($value )
                          ->loadAbsence($absences[3])->state($state2)->allowedJustification(!$value )
                          ->loadAbsence($absences[4])->state($state1)->allowedJustification($value )
                          ->loadAbsence($absences[5])->state($state1)->allowedJustification(!$value )
                          ->loadAbsence($absences[6])->state($state2)->allowedJustification($value )
                          ->loadAbsence($absences[7])->state($state2)->allowedJustification(!$value )
                          ->execute(true);

// Throw Exception
//new AbsenceUpdateBuilder()->allowedJustification(true)->execute(true);
//new AbsenceUpdateBuilder()->state($state1)->execute(true);
//new AbsenceUpdateBuilder()->loadAbsence($absences[0])->state($state1)->state($state2)->execute(true);
//new AbsenceUpdateBuilder()->loadAbsence($absences[0])->allowedJustification(true)->allowedJustification(true)->execute(true);
*/