<?php
namespace Uphf\GestionAbsence\Model\DB\Update;

use PDO;
use Uphf\GestionAbsence\Model\DB\Connection;

class HolidayUpdater
{
    public static function update(int $id, string $startDate, string $endDate, string $name): bool {

        $pdo = Connection::getInstance();

        $sql = 'UPDATE holidays
                SET HolidayName = :name,
                    startDate = :start,
                    endDate = :end
                WHERE holidaysid = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->bindValue(':start', $startDate ?: null, PDO::PARAM_STR);
        $stmt->bindValue(':end', $endDate ?: null, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }


    public static function delete(int $id): bool{

        $pdo = Connection::getInstance();

        $sql = 'DELETE FROM holidays WHERE holidayid = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}