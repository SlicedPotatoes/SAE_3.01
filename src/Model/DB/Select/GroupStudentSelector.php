<?php

namespace Uphf\GestionAbsence\Model\DB\Select;

use Uphf\GestionAbsence\Model\DB\Connection;
use Uphf\GestionAbsence\Model\Entity\Account\GroupStudent;
use Uphf\GestionAbsence\Model\Hydrator\GroupStudentHydrator;

/**
 * Classe static pour la récupération d'un objet GroupStudent
 */
class GroupStudentSelector
{
    public static function getGroupStudentByLabel(string $label) : GroupStudent | null
    {
        $pdo = Connection::getInstance();

        $query = "SELECT * FROM groupstudent WHERE grouplabel = :label";

        $sql = $pdo->prepare($query);
        $sql->bindValue(':label', $label);
        $sql->execute();

        $result = $sql->fetch();

        if ($result)
        {
            return GroupStudentHydrator::unserializeGroupStudent($result);
        }

        return null;
    }
}