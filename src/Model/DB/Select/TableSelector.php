<?php

namespace Uphf\GestionAbsence\Model\DB\Select;

use Uphf\GestionAbsence\Model\DB\Connection;
use InvalidArgumentException;
use PDO;

/**
 * Classe utilitaire pour effectuer des requêtes très basique sur la BDD
 */
class TableSelector {

    /**
     * Récupère l'ensemble des données de la table passée en paramètre
     *
     * IMPORTANT: Ne jamais passer de données fournies par l'utilisateur à \$table
     *
     * @param string $table
     * @return array
     */
    public static function fromTable(string $table): array {
        $conn = Connection::getInstance();

        $query = "SELECT * FROM $table";

        $sql = $conn->query($query);
        $sql->execute();

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer les lignes d'une table spécifique en appliquant une restriction (WHERE)
     *
     * Chaque case \$values[\$i] est un tableau:
     * - la première valeur est la valeur que l'on souhaite pour la colonne \$columns[\$i]
     * - la seconde est le type de la valeur, PDO::PARAM_X
     *
     * IMPORTANT: Ne jamais passer de données fournies par l'utilisateur à \$table ou à \$columns
     *
     * @param string $table
     * @param array $columns
     * @param array $values
     * @return array|null
     * @throws InvalidArgumentException Si le nombre d'éléments dans columns est différent de celui de values, ou s'il est null.
     */
    public static function fromTableWhere(string $table, array $columns, array $values): array | null {
        if(empty($columns)) {
            throw new InvalidArgumentException("Veuillez spécifier des colonnes et valeurs associer");
        }
        if(count($columns) != count($values)) {
            throw new InvalidArgumentException("Le nombre d'éléments dans columns dois être égale a celui de values");
        }

        $conn = Connection::getInstance();

        $where = [];

        foreach($columns as $column) {
            $where[] = "$column = :$column";
        }

        $sql = $conn->prepare("SELECT * FROM $table WHERE " . implode(" AND ", $where));

        for($i = 0; $i < count($columns); $i++) {
            $sql->bindValue(":$columns[$i]", $values[$i][0], $values[$i][1]);
        }

        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
}

/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . "/../../../../vendor/autoload.php";

$values = TableSelector::fromTable("GroupStudent");
var_export($values);

$value = TableSelector::fromTableByColumnsValuesUnique("Account", ["idAccount"], [[1, PDO::PARAM_INT]]);
var_export($value);
*/