<?php

namespace Uphf\GestionAbsence\Service;

use Uphf\GestionAbsence\Database\Select\GroupStudentSelector;

class GroupService
{
    /**
     * Récupération de tous les groupes d'étudiants
     *
     * @return array
     */
    public static function selectAllGroup() : array
    {
        return GroupStudentSelector::getAllGroup();
    }
}