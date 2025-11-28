<?php
/**
 * Script du lancement des routines
 *
 * Actuellement:
 * - Détection de retour
 * - Détection de longue absence
 */

use Dotenv\Dotenv;
use Uphf\GestionAbsence\Model\DB\Connection;
use Uphf\GestionAbsence\Routine\RoutineLoungAbsencesMail;
use Uphf\GestionAbsence\Routine\RoutineStudentComback;

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

// Initialisation du script
require_once dirname(__DIR__, 2) . "/vendor/autoload.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
$dotenv->load();

// Récupération de la date du jour, et check si "congé"
$today = new DateTime('now');
if(isOffPeriod($today)) {
    exit();
}

// Exécution des routines
Connection::beginTransaction();

RoutineStudentComback::executeRoutine(DateTime::createFromFormat("Y-m-d", "2025-11-28"));
RoutineLoungAbsencesMail::executeRoutine();

Connection::commit();