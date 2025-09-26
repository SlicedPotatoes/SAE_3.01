<?php

class Student
{
    private $firstName;
    private $lastName;
    private $email;
    private $studentId;
    private $firstName2;

    public function __construct($firstName, $lastName, $email, $studentId, $firstName2)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->studentId = $studentId;
        $this->firstName2 = $firstName2;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getStudentId()
    {
        return $this->studentId;
    }

    public function getFirstName2()
    {
        return $this->firstName2;
    }
}