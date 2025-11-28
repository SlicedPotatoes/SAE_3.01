<?php

namespace Uphf\GestionAbsence\Model\Hydrator;

use Uphf\GestionAbsence\Model\Entity\Account\GroupStudent;

class GroupStudentHydrator
{
    public static function unserializeGroupStudent(array $raw): GroupStudent
    {
        return new GroupStudent(
          $raw['groupid'],
          $raw['grouplabel']
        );
    }
}