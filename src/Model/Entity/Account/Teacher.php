<?php
namespace Uphf\GestionAbsence\Model\Entity\Account;

use Uphf\GestionAbsence\Model\DB\Select\MailAlertSelector;

/**
 * Classe Teacher, basé sur la base de données.
 */
class Teacher extends Account {
    private bool $mailAlert;

    function __construct($idTeacher, $lastName, $firstName, $email) {
        parent::__construct($idTeacher, $lastName, $firstName, $email,AccountType::Teacher);
    }

    public function getMailAlert() : bool
    {
        if ($this->mailAlert !== null) {
            return $this->mailAlert;
        }
        else {
            $this->mailAlert = MailAlertSelector::MailAlertTeacherIsActivated($this->getIdAccount());
            return $this->mailAlert;
        }
    }
}