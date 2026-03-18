<?php

namespace Uphf\GestionAbsence\Database\Select;

use Uphf\GestionAbsence\Database\Connection;
use Uphf\GestionAbsence\Model\Entity\Account\GroupStudent;
use Uphf\GestionAbsence\Model\Hydrator\GroupStudentHydrator;

/**
 * Classe static chaque méthode permet de récupérer un/des objets de type GroupStudent depuis la BDD
 */
class GroupStudentSelector
{
    /**
     * Récupération d'un objet GroupStudent par rapport a son label
     *
     * @param string $label
     * @return GroupStudent|null
     */
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

    /**
     * Récupérer l'ensemble des GroupStudent
     *
     * @return GroupStudent[]
     */
    public static function getAllGroup(): array {
        $res = TableSelector::fromTable("GroupStudent");

        $result = [];

        foreach ($res as $row) {
            $result[] = GroupStudentHydrator::unserializeGroupStudent($row);
        }

        return $result;
    }
}