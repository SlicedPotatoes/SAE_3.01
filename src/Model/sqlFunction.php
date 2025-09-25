<?php
require_once("connection.php");

function getAbsencesStudent($idStudent)
{
    $request = $connection->prepare("select * from absences where idStudent=?");
    $request->bindParam(1, $idStudent);
    $request->execute();
    $result = $request->fetch();
    return $result;
}

function getAbsencesStudentNotJustified($idStudent)
{
    $request = $connection->prepare("select * from absences join States using(idStates) where idStudent=? and idStates = 'Non Justifier'");
    $request->bindParam(1, $idStudent);
    $request->execute();
    $result = $request->fetch();
    return $result;
}

function getAbsencesBetween($idStudent, $start, $end)
{
    $request = $connection->prepate("select * from absences where idStudent=? and time between '$start' and '$end'");
    $request->bindParam(1, $idStudent);
    $request->bindParam(2, $start);
    $request->bindParam(3, $end);
    $request->execute();
    $result = $request->fetch();
    return $result;
}

function getAbsencesStudentByRessource($idStudent, $idRessource)
{
    $request = $connection->prepare("select * from absences where idStudent=? and idRessource=?");
    $request->bindParam(1, $idStudent);
    $request->bindParam(2, $idRessource);
    $request->execute();
    $result = $request->fetch();
    return $result;
}

function getAbsencesStudentByCourseType($idStudent, $idCourseType){
    $request = $connection->prepare("select * from absences where idStudent=? and idCourseType=?");
    $request->bindParam(1, $idStudent);
    $request->bindParam(2, $idCourseType);
    $request->execute();
    $result = $request->fetch();
    return $result;
}

function getAbsencesStudentByTeacher($idStudent, $idTeacher)
{
$request = $connection->prepare("select * from absences join ressources using(idTeacher) where idStudent=? and idTeacher=?)");
$request->bindParam(1, $idStudent);
$request->bindParam(2, $idTeacher);
$request->execute();
$result = $request->fetch();
return $result;
}

