<?php

namespace Uphf\GestionAbsence\Model\DB\Update;

use PDO;
use Uphf\GestionAbsence\Model\DB\Connection;

/**
 * Classe qui met à disposition des méthodes statiques pour mettre à jour un Semester dans la BDD
 */
class SemesterUpdater {

    /**
     * Mettre à jour les dates d'un semestre dans la BDD
     *
     * @param int $id
     * @param string $startDate
     * @param string $endDate
     * @return bool
     */
    public static function update(int $id, string $startDate, string $endDate): bool {
        $pdo = Connection::getInstance();

        $sql = 'UPDATE semester
                SET startDate = :start,
                    endDate = :end
                WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':start', $startDate, PDO::PARAM_STR);
        $stmt->bindValue(':end', $endDate, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
