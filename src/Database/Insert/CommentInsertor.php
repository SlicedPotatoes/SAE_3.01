<?php

namespace Uphf\GestionAbsence\Database\Insert;

use Uphf\GestionAbsence\Database\Connection;

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
