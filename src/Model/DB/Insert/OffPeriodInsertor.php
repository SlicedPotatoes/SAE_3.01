<?php
namespace Uphf\GestionAbsence\Model\DB\Insert;

use Uphf\GestionAbsence\Model\DB\Connection;

/**
 * Classe qui expose une méthode statique pour insérer une OffPeriod dans la BDD
 */
class OffPeriodInsertor
{
    /**
     * Insérer une OffPeriod dans la BDD
     *
     * @param string $startDate
     * @param string $endDate
     * @param string $label
     * @return bool
     */
    public static function insert(string $startDate, string $endDate, string $label): bool
    {
        $pdo = Connection::getInstance();

        $sql = 'INSERT INTO offperiod (startDate, endDate, label) VALUES (:start, :end, :label)';
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            ':start' => $startDate,
            ':end'   => $endDate,
            ':label' => $label,
        ]);
    }
}
