<?php

class Teacher {
    private int $idTeacher;
    private string $lastName;
    private string $firstName;
    private string $email;


    function __construct($idTeacher, $lastName, $firstName, $email) {
        $this->idTeacher = $idTeacher;
        $this->lastName = $lastName;
        $this->firstName = $firstName;
        $this->email = $email;
    }

    function getIdTeacher(): int { return $this->idTeacher; }
    function getLastName(): string { return $this->lastName; }
    function getFirstName(): string { return $this->firstName; }
    function getEmail(): string { return $this->email; }
}