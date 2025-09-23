<?php
require_once("connection.php");

function absencesStudent($idStudent)
{
    $request = $connection->prepare("select * from absences where idStudent=?");
    $request->bindParam(1, $idStudent);
    $request->execute();
    $result = $request->fetch();
    return $result;
}

function absencesStudentNotJustified($idStudent)
{
    $request = $connection->prepare("select * from absences join States using(idStates) where idStudent=? and idStates = 'Non Justifier'");
    $request->bindParam(1, $idStudent);
    $request->execute();
    $result = $request->fetch();
    return $result;
}

function absencesBetween($idStudent, $start, $end)
{
    $request = $connection->prepate("select * from absences where idStudent=? and time between '$start' and '$end'");
    $request->bindParam(1, $idStudent);
    $request->bindParam(2, $start);
    $request->bindParam(3, $end);
    $request->execute();
    $result = $request->fetch();
    return $result;
}

function AbsencesStudentByRessource($idStudent, $idRessource)
{
    $request = $connection->prepare("select * from absences where idStudent=? and idRessource=?");
    $request->bindParam(1, $idStudent);
    $request->bindParam(2, $idRessource);
    $request->execute();
    $result = $request->fetch();
    return $result;
}

function AbsencesStudentByCourseType($idStudent, $idCourseType){
    $request = $connection->prepare("select * from absences where idStudent=? and idCourseType=?");
    $request->bindParam(1, $idStudent);
    $request->bindParam(2, $idCourseType);
    $request->execute();
    $result = $request->fetch();
    return $result;
}

function AbsencesStudentByTeacher($idStudent, $idTeacher)
{
$request = $connection->prepare("select * from absences join ressources using(idTeacher) where idStudent=? and idTeacher=?)");
$request->bindParam(1, $idStudent);
$request->bindParam(2, $idTeacher);
$request->execute();
$result = $request->fetch();
return $result;
}