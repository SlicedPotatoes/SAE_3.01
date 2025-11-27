<?php

use Dotenv\Dotenv;
use Uphf\GestionAbsence\Model\DB\Connection;
use Uphf\GestionAbsence\Routine\RoutineLoungAbsencesMail;
use Uphf\GestionAbsence\Routine\RoutineStudentComback;

require_once dirname(__DIR__) . "/vendor/autoload.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$dotenv = Dotenv::createImmutable(dirname((__DIR__)));
$dotenv->load();

$today = new DateTime('now');

/**
 * Prend une date, renvoie vrai si la date est en offperiod
 *
 * Une date est en offPeriod si
 * - Elle match avec une plage ce trouvant dans la BDD
 * - Le jour de la semaine est samedi ou dimanche
 *
 * @param $date
 * @return bool
 */
function isOffPeriod($date): bool {
    /**
     * Le format N permet de récupérer un numéro correspondant au jour de la semaine
     * 1 pour lundi, 7 pour dimanche
     *
     * Donc si on est en weekend, on est en offPeriod
     */
    if($date->format("N") > 5) { return true; }

    $pdo = Connection::getInstance();

    $query = "SELECT COUNT(*)
              FROM offPeriod
              WHERE startDate <= :date::timestamp AND endDate + '1 day' >= :date::timestamp";

    $sql = $pdo->prepare($query);
    $sql->bindValue(':date', $date->format('Y-m-d'));
    $sql->execute();

    $res = $sql->fetch(PDO::FETCH_NUM);

    return $res[0] === 1;
}

if(isOffPeriod($today)) {
    exit();
}

RoutineStudentComback::executeRoutine($today);
RoutineLoungAbsencesMail::executeRoutine();