<?php

namespace Uphf\GestionAbsence\Model\DB\Insert;

use Uphf\GestionAbsence\Model\DB\Connection;
use PDO;

class CommentInsertor
{
    /**
     * Insère un nouveau commentaire prédéfini
     *
     * @param string $textComment Le texte du commentaire
     * @return bool True si l'insertion a réussi
     */
    public static function insert(string $textComment) : bool {
        $pdo = Connection::getInstance();

        $query = "INSERT INTO comments (textcomment) VALUES (:textcomment)";
        $stmt = $pdo->prepare($query);

        return $stmt->execute([':textcomment' => $textComment]);
    }
}
