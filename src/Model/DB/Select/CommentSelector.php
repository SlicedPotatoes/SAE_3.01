<?php

namespace Uphf\GestionAbsence\Model\DB\Select;

use Uphf\GestionAbsence\Model\DB\Connection;
use Uphf\GestionAbsence\Model\Entity\Comment\Comment;
use Uphf\GestionAbsence\Model\Hydrator\CommentHydrator;
use PDO;

/**
 * Classe pour sélectionner les commentaires prédéfinis
 */
class CommentSelector
{
    /**
     * Récupère tous les commentaires prédéfinis
     * 
     * @return Comment[]
     */
    public static function getAllComments(): array
    {
        $pdo = Connection::getInstance();
        $query = "SELECT idcomment, textcomment FROM comments ORDER BY idcomment ASC";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        
        $comments = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $comments[] = CommentHydrator::unserializeComment($row);
        }
        
        return $comments;
    }

    /**
     * Récupère un commentaire par son ID
     * 
     * @param int $idComment
     * @return Comment|null
     */
    public static function getCommentById(int $idComment): ?Comment
    {
        $pdo = Connection::getInstance();
        $query = "SELECT idcomment, textcomment FROM comments WHERE idcomment = :idComment";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['idComment' => $idComment]);
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) {
            return null;
        }
        
        return CommentHydrator::unserializeComment($row);
    }
}
