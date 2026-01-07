<?php

namespace Uphf\GestionAbsence\Model\DB\Select\SelectBuilder;

use DateTime;
use http\Exception\BadMethodCallException;
use Uphf\GestionAbsence\Model\DB\Connection;
use Uphf\GestionAbsence\Model\Entity\Absence\StateAbs;

class AbsenceMouthlyCountSelectBuilder
{
    private array $where = [];
    private array $parameters = [];
    private array $flags = [];

    private string $startTime;
    private string $endTime;

    public function __construct()
    {
        self::currentYear();
        $this->parameters['startTime'] = $this->startTime;
        $this->parameters['endTime'] = $this->endTime;
    }

    function currentYear(): void
    {
        $now = new DateTime();

        $year = (int) $now->format('Y');
        $month = (int) $now->format('n');

        $startDate = ($month >= 9) ? $year : $year - 1;

        $this->startTime = $startDate . '-09-01';
        $this->endTime = ($startDate + 1) . '-09-01';
    }

    public function idStudent(int $idStudent): self
    {
        if (isset($this->flags['idStudent'])) {
            throw new BadMethodCallException("Second appel de la méthode 'idStudent()'.");
        }
        $this->flags[$idStudent] = true;

        $this->where[] = "idStudent = :idStudent";
        $this->parameters["idStudent"] = $idStudent;

        return $this;
    }

    public function state(StateAbs $stateAbs): self
    {
        if (isset($this->flags['state'])) {
            throw new BadMethodCallException("Second appel de la méthode 'state()'.");
        }
        $this->flags['state'] = true;

        $this->where[] = "currentState = :state";
        $this->parameters["state"] = $stateAbs->value;

        return $this;
    }

    public function execute(bool $debug = false): array
    {
        $query = "
        SELECT 
            date_trunc('month', time) AS month, 
            COUNT(*) AS absence_count 
        FROM absence 
        WHERE time >= :startTime 
            AND time < :endTime";

        if(!empty($this->where)) {
            $query .= " WHERE " . implode(" AND ", $this->where);
        }

        $query .= " GROUP BY month ORDER BY month";

        if ($debug) {
            echo "<pre>$query</pre>";
        }

        $sql = Connection::getInstance()->prepare($query);

        foreach($this->parameters as $key => [$value, $type]) {
            $sql->bindValue(':'.$key, $value, $type);
        }

        $sql->execute();
        return $sql->fetchAll();
    }
}