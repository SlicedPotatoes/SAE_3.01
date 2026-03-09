<?php

namespace Uphf\GestionAbsence\Database\Delete;

use Uphf\GestionAbsence\Database\Connection;

class TokenDelete {
    public static function deleteToken($token): void {
        $pdo = Connection::getInstance();

        $query = "DELETE FROM TokenPassword WHERE token = :token";

        $sql = $pdo->prepare($query);
        $sql->bindValue(":token", $token);
        $sql->execute();
    }
}