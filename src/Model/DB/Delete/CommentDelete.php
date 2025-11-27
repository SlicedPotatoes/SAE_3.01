<?php

namespace Uphf\GestionAbsence\Model\DB\Delete;

use Uphf\GestionAbsence\Model\DB\Connection;

class CommentDelete{
    /**
     * Supprime un commentaire prédéfini
     *
     * @param int $idComment L'ID du commentaire à supprimer
     * @return bool True si la suppression a réussi
     */
    public static function delete(int $idcomment) : bool {
        $pdo = Connection::getInstance();

        $query = "DELETE FROM comments WHERE idcomment = :idcomment";
        $stmt = $pdo->prepare($query);

        return $stmt->execute([':idcomment' => $idcomment]);
    }
}
