<?php

namespace Uphf\GestionAbsence\Model\DB\Select;

use Uphf\GestionAbsence\Model\DB\Connection;
use Uphf\GestionAbsence\Model\Entity\OffPeriod;
use Uphf\GestionAbsence\Model\Hydrator\OffPeriodHydrator;
use PDO;

/**
 * Classe static, mettant a disposition une méthode pour sélectionner les périodes de congés depuis la BDD
 */
class OffPeriodSelector{

    /**
     * Récupérer la liste des OffPeriod depuis la BDD
     *
     * @return OffPeriod[]
     */
    public static function getOffPeriod(): array {
        $rows = TableSelector::fromTable('offPeriod');

        $res = [];
        foreach($rows as $row) {
            $res[] = OffPeriodHydrator::unserializeHoliday($row);
        }

        return $res;
    }

}


