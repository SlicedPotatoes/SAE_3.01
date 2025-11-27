<?php

namespace Uphf\GestionAbsence\Model\Hydrator;

use Uphf\GestionAbsence\Model\Entity\Absence\CourseType;
use Uphf\GestionAbsence\Model\Entity\Absence\Resource;
use Uphf\GestionAbsence\Model\Entity\Absence\StateAbs;
use Uphf\GestionAbsence\Model\Entity\Absence\TimeSlotAbsence;
use Uphf\GestionAbsence\Model\Entity\Account\Teacher;
use DateTime;

class TimeSlotAbsenceHydrator
{
public static function unserializeTimeSlotAbsence(array $raw1,array|null $raw2): TimeSlotAbsence
{
    if($raw2 !== null){
        $csaj = $raw2['countstudentsabsencesjustified'];
    }else{
        $csaj = 0;
    }
    return new TimeSlotAbsence(
        DateTime::createFromFormat("Y-m-d H:i:s", $raw1['time']),
        $raw1['examen'],
        $raw1['duration'],
        $raw1['countstudentsabsences'],
        $csaj,
        AccountHydrator::unserializeTeacher($raw1),
        CourseType::from($raw1['coursetype']),
        $raw1['groupe'],
        new Resource($raw1['idresource'],$raw1['label'])
    );
}
}