<?php

namespace Uphf\GestionAbsence\Model\DB;

use PDO;

/**
 * Singleton de connexion
 * Chaque développeur peut configurer une BDD Locale de test dans ce fichier
 */
class Connection
{
    private static array $testSettings = [
        "host" => "localhost",
        "port" => "5432",
        "user" => "postgres",
        "pass" => "12345",
        "dbname" => "postgres",
    ];

    private static array $prodSettings = [
        "host" => "tommytech.net",
        "port" => "5432",
        "user" => "kevin",
        "pass" => "patate360",
        "dbname" => "postgres",
    ];

    private static PDO | null $instance = null;
    private static bool $test = false;

    /**
     * Récupérer l'instance de la connexion PDO
     * @return PDO
     */
    public static function getInstance(): PDO
    {
        if (self::$instance == null)
        {
            if (self::$test)
            {
                $host   = self::$testSettings["host"];
                $port   = self::$testSettings["port"];
                $user   = self::$testSettings["user"];
                $pass   = self::$testSettings["pass"];
                $dbname = self::$testSettings["dbname"];

                echo "BDD Test";
            }
            else
            {
                $host   = self::$prodSettings["host"];
                $port   = self::$prodSettings["port"];
                $user   = self::$prodSettings["user"];
                $pass   = self::$prodSettings["pass"];
                $dbname = self::$prodSettings["dbname"];
            }
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