<?php

namespace Uphf\GestionAbsence\Model\DB\Select;

use PDO;
use Uphf\GestionAbsence\Model\DB\Connection;
use Uphf\GestionAbsence\Model\Entity\Account\Account;
use Uphf\GestionAbsence\Model\Hydrator\AccountHydrator;

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

    public static function getAccountFromToken(string $token): Account | null {
        $pdo = Connection::getInstance();

        $query = "SELECT * 
                  FROM TokenPassword 
                  JOIN Account USING(idAccount)
                  WHERE token = :token AND expire >= NOW()";

        $sql = $pdo->prepare($query);
        $sql->bindValue(":token", $token, PDO::PARAM_STR);
        $sql->execute();

        $res = $sql->fetch(PDO::FETCH_ASSOC);

        if($res) {
            return AccountHydrator::unserializeAccount($res);
        }

        return null;
    }

    public static function getAccountByEmail(string $email): Account | null
    {
        $pdo = Connection::getInstance();

        $query = "SELECT * 
                  FROM Account 
                  WHERE email = :email";

        $sql = $pdo->prepare($query);
        $sql->bindValue(":email", $email, PDO::PARAM_STR);
        $sql->execute();

        $res = $sql->fetch(PDO::FETCH_ASSOC);

        if($res) {
            return AccountHydrator::unserializeAccount($res);
        }

        return null;
    }
}