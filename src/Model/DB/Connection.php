<?php

namespace Uphf\GestionAbsence\Model\DB;

use PDO;
use Uphf\GestionAbsence\Model\GlobalVariable;

/**
 * Singleton de connexion
 * Chaque développeur peut configurer une BDD Locale de test dans ce fichier
 */
class Connection
{
    private static PDO | null $instance = null;

    /**
     * Récupérer l'instance de la connexion PDO
     * @return PDO
     */
    public static function getInstance(): PDO
    {
        if (self::$instance == null)
        {
            $suffix = "PROD";
            if (!GlobalVariable::PROD() && filter_var($_ENV['DB_TEST'], FILTER_VALIDATE_BOOL))
            {
                $suffix = "TEST";
                echo "BDD Test";
            }

            $host   = $_ENV["DB_HOST_$suffix"];
            $port   = $_ENV["DB_PORT_$suffix"];
            $user   = $_ENV["DB_USER_$suffix"];
            $pass   = $_ENV["DB_PASS_$suffix"];
            $dbname = $_ENV["DB_NAME_$suffix"];

            self::$instance = new PDO("pgsql:host=$host; dbname=$dbname; port=$port", $user, $pass);
        }
        return self::$instance;
    }

    public static function beginTransaction(): void {
        self::getInstance()->beginTransaction();
    }

    public static function commit(): void {
        self::getInstance()->commit();
    }

    public static function rollback(): void {
        self::getInstance()->rollBack();
    }

    public static function close(): void {
        self::$instance = null;
    }
}