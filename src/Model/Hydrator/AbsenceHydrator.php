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
        $studentRaw = [
            'studentid' => $raw['studentid'],
            'lastname' => $raw['studentlastname'],
            'firstname' => $raw['studentfirstname'],
            'email' => $raw['studentemail'],
            'accounttype' => $raw['studentaccounttype'],
            'studentnumber' => $raw['studentnumber'],
            'groupid' => $raw['groupid'],
            'grouplabel' => $raw['grouplabel']
        ];

        $teacherRaw = [];
        if(isset($raw['teacherid'])) {
            $teacherRaw['idaccount'] = $raw['teacherid'];
            $teacherRaw['lastname'] = $raw['teacherlastname'];
            $teacherRaw['firstname'] = $raw['teacherfirstname'];
            $teacherRaw['email'] = $raw['teacheremail'];
            $teacherRaw['accounttype'] = $raw['teacheraccounttype'];
        }

        return new Absence(
            AccountHydrator::unserializeStudent($studentRaw),
            DateTime::createFromFormat("Y-m-d H:i:s", $raw['time']),
            $raw['duration'],
            $raw['examen'],
            $raw['allowedjustification'],
            isset($raw['idteacher']) ? AccountHydrator::unserializeTeacher($teacherRaw) : null,
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