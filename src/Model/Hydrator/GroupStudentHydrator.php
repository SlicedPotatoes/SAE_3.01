<?php

namespace Uphf\GestionAbsence\Model\Hydrator;

use Uphf\GestionAbsence\Model\Entity\Account\GroupStudent;

/**
 * Hydrator permettant de récupérer un GroupStudent a partir des données brutes de la BDD
 */
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