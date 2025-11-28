<?php
namespace Uphf\GestionAbsence\Model\DB\Update;

use PDO;
use Uphf\GestionAbsence\Model\DB\Connection;

/**
 * Classe qui met à disposition des méthodes statiques pour mettre à jour une OffPeriod dans la BDD
 */
class OffPeriodUpdater
{
    /**
     * Mettre a jour les informations d'une offperiod dans la BDD
     *
     * @param int $id
     * @param string $startDate
     * @param string $endDate
     * @param string $name
     * @return bool
     */
    public static function update(int $id, string $startDate, string $endDate, string $name): bool {
        $pdo = Connection::getInstance();

        $sql = 'UPDATE offperiod
                SET label = :name,
                    startDate = :start,
                    endDate = :end
                WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->bindValue(':start', $startDate ?: null, PDO::PARAM_STR);
        $stmt->bindValue(':end', $endDate ?: null, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    /**
     * Supprimer une offperiod de la BDD
     *
     * @param int $id
     * @return bool
     */
    public static function delete(int $id): bool{
        $pdo = Connection::getInstance();

        $sql = 'DELETE FROM offperiod WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
