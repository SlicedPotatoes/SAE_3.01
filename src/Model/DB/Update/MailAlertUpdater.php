<?php

namespace Uphf\GestionAbsence\Model\DB\Update;

use PDO;
use Uphf\GestionAbsence\Model\DB\Connection;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;

class MailAlertUpdater
{

    public static function updateMailAlert(
        AccountType $role,
        int $idaccount,
        bool $mailAlertTeacher = true,
        bool $mailAlertEducationalManager = true)
    {
        $pdo = Connection::getInstance();
        $query = "UPDATE mailAlertTeacher 
                SET activated = :activated 
                WHERE idaccount = :idaccount";
        $stmt = $pdo->prepare($query);
        $stmt->bindValue(":activated", $mailAlertTeacher, PDO::PARAM_BOOL);
        $stmt->bindValue(":idaccount", $idaccount, PDO::PARAM_INT);
        $stmt->execute();

        // Si l'utilisateur est un RP il change également les obtions dans la table de la notification pour le rp
        if($role === AccountType::EducationalManager) {
            $query = "UPDATE mailAlertEducationalManager 
                    SET activated = :activated 
                    WHERE idaccount = :idaccount";
            $stmt = $pdo->prepare($query);
            $stmt->bindValue(":activated", $mailAlertEducationalManager, PDO::PARAM_BOOL);
            $stmt->bindValue(":idaccount", $idaccount, PDO::PARAM_INT);
            $stmt->execute();
        }
    }
}