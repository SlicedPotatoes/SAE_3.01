<?php

require_once __DIR__ . '/Account.php';

/**
 * Classe Teacher, basé sur la base de données.
 */
class Teacher extends Account {

    function __construct($idTeacher, $lastName, $firstName, $email) {
        parent::__construct($idTeacher, $lastName, $firstName, $email,AccountType::Teacher);
    }
}