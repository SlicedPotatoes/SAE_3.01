<?php

namespace Uphf\GestionAbsence\Model\DB\Insert;

use PDO;
use Uphf\GestionAbsence\Model\DB\Connection;

class TokenInsertor {
    public static function insertToken($idAccount, $token): void {
        $pdo = Connection::getInstance();

        $query = "INSERT INTO tokenpassword (token, idaccount) VALUES (:token, :idAccount)";

        $sql = $pdo->prepare($query);
        $sql->bindValue(':token', $token, PDO::PARAM_STR);
        $sql->bindValue(':idAccount', $idAccount, PDO::PARAM_INT);

        $sql->execute();
    }
}