<?php

namespace Uphf\GestionAbsence\Model\DB\Select;

use Uphf\GestionAbsence\Model\DB\Connection;
use Uphf\GestionAbsence\Model\Hydrator\HolidayHydrator;
use PDO;

class HolidaySelector{

    public static function getHolidays(): array {
        $conn = Connection::getInstance();

        $query = "SELECT * FROM Holidays";

        $sql = $conn->prepare($query);
        $sql->execute();

        $rows = $sql->fetchAll(PDO::FETCH_ASSOC);
        $holidays = [];

        foreach($rows as $row) {
            $holidays[] = HolidayHydrator::unserializeHoliday($row);
        }

        return $holidays;
    }

}


