<?php
//Cette classe représente les enseignants dans la base donnée
class Teacher extends Account {

    function __construct($idTeacher, $lastName, $firstName, $email) {
        parent::__construct($idTeacher, $lastName, $firstName, $email);
    }
}