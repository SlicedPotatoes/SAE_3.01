<?php

class StudentPresentation
{
    public static function getStudentAccountDashboard() : null | Student
    {
        if ($_SESSION["role"] == AccountType::Student)
        {
            return $_SESSION["account"];
        }
        else
        {
            if (isset($_GET['studentAccount']))
            {
                return Student::getStudentByIdAccount($_GET['studentAccount']);
            }
            else
            {
                header("location: index.php?currPage=404");
                exit();
            }
        }
    }
}
