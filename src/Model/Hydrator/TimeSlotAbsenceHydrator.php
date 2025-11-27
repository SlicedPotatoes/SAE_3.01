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
    public static function unserializeTimeSlotAbsence(array $raw1, ?array $raw2): TimeSlotAbsence
    {
        // Sécuriser la création de la DateTime
        $time = DateTime::createFromFormat('Y-m-d H:i:s', $raw1['time']);

        $countJustified = 0;
        if ($raw2 !== null && isset($raw2['countstudentsabsencesjustified']))
        {
            $countJustified = (int) $raw2['countstudentsabsencesjustified'];
        }

        return new TimeSlotAbsence(
          $time,
          (bool) $raw1['examen'],
          $raw1['duration'],
          (int) $raw1['countstudentsabsences'],
          $countJustified,
          AccountHydrator::unserializeTeacher($raw1),
          CourseType::from($raw1['coursetype']),
          $raw1['groupe'] ?? null,
          new Resource($raw1['idresource'], $raw1['label'])
        );
    }
}