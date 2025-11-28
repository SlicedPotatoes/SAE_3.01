<?php

namespace Uphf\GestionAbsence\Model\DB\Update;

use Uphf\GestionAbsence\Model\DB\Connection;

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