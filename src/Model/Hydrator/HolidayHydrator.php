<?php

namespace Uphf\GestionAbsence\Model\Hydrator;

use DateTime;
use Uphf\GestionAbsence\Model\Entity\Account\Student;
use Uphf\GestionAbsence\Model\Entity\Holiday;

class HolidayHydrator {

    public static function unserializeHoliday(array $raw): Holiday {
        return new Holiday(
            $raw['holidayid'],
            $raw['holidayname'],
            DateTime::createFromFormat('Y-m-d', $raw['startdate']),
            DateTime::createFromFormat('Y-m-d', $raw['enddate']),
        );
    }

}