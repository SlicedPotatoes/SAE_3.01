<?php
namespace Uphf\GestionAbsence\Model\Entity\Account;

/**
 * Classe Teacher, basé sur la base de données.
 */
class Teacher extends Account {

    function __construct($idTeacher, $lastName, $firstName, $email) {
        parent::__construct($idTeacher, $lastName, $firstName, $email,AccountType::Teacher);
    }
}