<?php

namespace Uphf\GestionAbsence\Database\Update;

use Uphf\GestionAbsence\Database\Connection;

class CommentUpdater{
    /**
     * @param int $idcomment
     * @param string $textcomment
     * @return bool
     */
    public static function update(int$idcomment, string $textcomment) : bool{
        $pdo = Connection::getInstance();
        $query = "UPDATE comments SET textcomment = :textcomment WHERE idcomment = :idcomment";
        $stmt = $pdo->prepare($query);

        return $stmt->execute([
            "idcomment" => $idcomment,
            "textcomment" => $textcomment
        ]);
    }
}