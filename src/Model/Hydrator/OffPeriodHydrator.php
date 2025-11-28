<?php

namespace Uphf\GestionAbsence\Model\Hydrator;

use DateTime;
use Uphf\GestionAbsence\Model\Entity\OffPeriod;

/**
 * Hydrator pattern, permettant de récupérer une entité OffPeriod à partir de données brutes
 */
class OffPeriodHydrator {

    public static function unserializeHoliday(array $raw): OffPeriod {
        return new OffPeriod(
            $raw['id'],
            $raw['label'],
            DateTime::createFromFormat('Y-m-d h:i:s', $raw['startdate']),
            DateTime::createFromFormat('Y-m-d h:i:s', $raw['enddate']),
        );
    }

}