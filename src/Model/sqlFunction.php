<?php
require_once("connection.php");

function absencesStudent($idStudent){
$request = $connection->prepare("select * from absences where idStudent=?");
$request->bindParam(1, $idStudent);
$request->execute();
$result = $request->fetch();
return $result;
}
function absencesStudentNotJustified($idStudent){
    $request = $connection->prepare("select * from absences join States using(idStates) where idStudent=? and idStates = 'Non Justifier'");
    $request->bindParam(1, $idStudent);
    $request->execute();
    $result = $request->fetch();
    return $result;
}

function absencesBetween($idStudent, $start, $end){
    $request = $connection->prepate()
}