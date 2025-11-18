<?php

namespace Uphf\GestionAbsence\Model\Hydrator;

use Uphf\GestionAbsence\Model\Entity\Absence\Absence;
use Uphf\GestionAbsence\Model\Entity\Absence\CourseType;
use Uphf\GestionAbsence\Model\Entity\Absence\Resource;
use Uphf\GestionAbsence\Model\Entity\Absence\StateAbs;
use DateTime;

/**
 * Hydrator pattern, permettant de récupérer une entité Absence à partir de données brutes
 */
class AbsenceHydrator {
    public static function unserializeAbsence(array $raw): Absence {
        return new Absence(
            AccountHydrator::unserializeStudent($raw),
            DateTime::createFromFormat("Y-m-d H:i:s", $raw['time']),
            $raw['duration'],
            $raw['examen'],
            $raw['allowedjustification'],
            isset($raw['idteacher']) ? AccountHydrator::unserializeTeacher($raw) : null,
            StateAbs::from($raw['currentstate']),
            CourseType::from($raw['coursetype']),
            new Resource(
                $raw['idresource'],
                $raw['label']
            ),
            isset($raw['dateresit']) ? DateTime::createFromFormat("Y-m-d H:i:s", $raw['dateresit']) : null
        );
    }
}