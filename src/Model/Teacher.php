<?php

class Teacher
{
    private $firstName;
    private $lastName;
    private $email;
    private $id;

    function __construct($firstName, $lastName, $email, $id)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->id = $id;
    }

    function getFirstName()
    {
        return $this->firstName;
    }

    function getLastName()
    {
        return $this->lastName;
    }

    function getEmail()
    {
        return $this->email;
    }

    function getId()
    {
        return $this->id;
    }
}