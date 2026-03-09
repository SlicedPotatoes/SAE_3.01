<?php

namespace Uphf\GestionAbsence\Database\Update;
use Uphf\GestionAbsence\Database\Connection;

/**
 * Classe exposant une méthode statique pour permettre le changement d'un mot de passe dans la BDD
 */
class PasswordUpdate{

    /**
     * Changer le mot de passe d'un utilisateur
     *
     * @param int $idAccount
     * @param string $newPasswordHash
     * @return bool
     */
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