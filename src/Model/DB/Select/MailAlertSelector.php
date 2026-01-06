<?php

namespace Uphf\GestionAbsence\Model\DB\Select;

use Uphf\GestionAbsence\Model\DB\Connection;

class MailAlertSelector
{
    public static function MailAlertEducationalManagerIsActivated(int $idAccount):bool
    {
        $pdo = Connection::getInstance();
        $query = $pdo->prepare('SELECT activated FROM account join mailAlertEducationalManager using (idaccount) WHERE idAccount = :idAccount;');
        $query->bindValue(':idAccount', $idAccount);
        $query->execute();
        $result = $query->fetch();
        return $result;
    }

    public static function MailAlertTeacherIsActivated(int $idAccount):bool
    {
        $pdo = Connection::getInstance();
        $query = $pdo->prepare('SELECT activated FROM account join mailAlertTeacher using (idaccount) WHERE idAccount = :idAccount;');
        $query->bindValue(':idAccount', $idAccount);
        $query->execute();
        $result = $query->fetch();
        return $result;
    }
    public static function MailAlertSIsActivated(int $idAccount):array
    {
        $pdo = Connection::getInstance();
        $query = $pdo->prepare('SELECT mailAlertTeacher.activated as mailAlertTeacher,  COALESCE(mailAlertEducationalManager.activated,false) as mailAlertEducationalMannager FROM account join mailAlertTeacher using (idaccount) left join mailAlertEducationalManager using (idaccount) WHERE account.idAccount = :idAccount;');
        $query->bindValue(':idAccount', $idAccount);
        $query->execute();
        $result = $query->fetch();
        return $result;
    }
}
