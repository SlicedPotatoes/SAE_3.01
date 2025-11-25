<?php

namespace Uphf\GestionAbsence\Model\DB\Select;

use PDO;
use Uphf\GestionAbsence\Model\DB\Connection;

class AccountSelector {
    public static function getPasswordHashedById(int $idAccount): string | null {
        $pdo = Connection::getInstance();

        $query = "SELECT password FROM Account WHERE idAccount = ?";

        $sql = $pdo->prepare($query);
        $sql->execute([$idAccount]);

        $res = $sql->fetch(PDO::FETCH_ASSOC);

        if($res) {
            return $res['password'];
        }

        return null;
    }
}