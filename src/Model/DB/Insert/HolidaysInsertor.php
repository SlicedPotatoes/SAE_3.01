<?php
namespace Uphf\GestionAbsence\Model\DB\Insert;

use Uphf\GestionAbsence\Model\DB\Connection;

class HolidaysInsertor
{
    public static function insert(string $startDate, string $endDate, string $label): bool
    {
        $pdo = Connection::getInstance();

        $sql = 'INSERT INTO holidays (startDate, endDate, HolidayName) VALUES (:start, :end, :label)';
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            ':start' => $startDate,
            ':end'   => $endDate,
            ':label' => $label,
        ]);
    }
}
