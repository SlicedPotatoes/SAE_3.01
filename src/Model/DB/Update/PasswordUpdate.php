<?php

namespace Uphf\GestionAbsence\Model\DB\Update;
use Uphf\GestionAbsence\Model\DB\Connection;
use PDO;

class PasswordUpdate{
    public static function updatePassword(int $idAccount, string $newPasswordHash): bool {
        $pdo = Connection::getInstance();
        $query = "UPDATE Account SET password = :password WHERE idAccount = :idAccount";
        $statement = $pdo->prepare($query);

        return $statement->execute([
            'password'=>$newPasswordHash,
            'idAccount'=>$idAccount
        ]);
    }

}