<?php

namespace Uphf\GestionAbsence\Model\Hydrator;

use DateTime;
use Uphf\GestionAbsence\Model\Entity\Semester;

/**
 * Hydrator pattern, permettant de récupérer une entité Semester à partir de données brutes
 */
class SemesterHydrator {

    public static function unserialize(array $raw): Semester {
        return new Semester(
            $raw['id'],
            $raw['idacademicyear'],
            $raw['label'],
            DateTime::createFromFormat('Y-m-d', $raw['startdate']),
            DateTime::createFromFormat('Y-m-d', $raw['enddate']),
        );
    }
}
