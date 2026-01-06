<?php

namespace Uphf\GestionAbsence\Model\DB\Select;

use Uphf\GestionAbsence\Model\DB\Connection;

class MailAlertSelector
{
    public static function MailAlertSIsActivated(int $idAccount):array
    {
        $pdo = Connection::getInstance();
        $query = $pdo->prepare('SELECT mailAlertTeacher.activated as mailAlertTeacher, mailAlertEducationalManager.activated as mailAlertEducationalMannager FROM account join mailAlertTeacher using (idaccount) left join mailAlertEducationalManager using (idaccount) WHERE account.idAccount = :idAccount;');
        $query->bindValue(':idAccount', $idAccount);
        $query->execute();
        $result = $query->fetch();
        return $result;
    }
}
var_dump(MailAlertSelector::MailAlertSIsActivated(1));