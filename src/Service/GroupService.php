<?php

namespace Uphf\GestionAbsence\Service;

use Uphf\GestionAbsence\Database\Select\GroupStudentSelector;
use Uphf\GestionAbsence\Model\Entity\Account\GroupStudent;

class GroupService
{
    /**
     * Récupération de tous les groupes d'étudiants
     *
     * @return GroupStudent[]
     */
    public static function selectAllGroup() : array
    {
        return GroupStudentSelector::getAllGroup();
    }
}