<?php

namespace Uphf\GestionAbsence\Service;

use InvalidArgumentException;
use Uphf\GestionAbsence\Database\Delete\CommentDelete;
use Uphf\GestionAbsence\Database\Insert\CommentInsertor;
use Uphf\GestionAbsence\Database\Select\CommentSelector;
use Uphf\GestionAbsence\Database\Update\CommentUpdater;
use Uphf\GestionAbsence\Model\Entity\Comment\Comment;

/**
 * Service pour les commentaires prédéfinis
 */
class PredifinedCommentService
{
    /**
     * Supprime un commentaire prédéfini grâce à son ID
     *
     * @param int $idcomment
     * @return bool
     */
    public static function commentDelete(int $idcomment): bool
    {
        return CommentDelete::delete($idcomment);
    }

    /**
     * Insère un commentaire prédéfini dans la base de données
     *
     * @param string $textComment
     * @return bool
     */
    public static function commentInsertor(string $textComment): bool
    {
        return CommentInsertor::insert($textComment);
    }

    /**
     * Récupère tous les commentaires prédéfinis de la base de données
     *
     * @return array
     */
    public static function commentSelectorAll(): array
    {
        return CommentSelector::getAllComments();
    }

    /**
     * Récupère un commentaire prédéfini grâce à son ID
     *
     * @param int $idComment
     * @return Comment
     * @throws InvalidArgumentException dans le cas où aucun commentaire n'est trouvé avec l'ID donné
     */
    public static function commentSelectorById(int $idComment): Comment
    {
        $comment = CommentSelector::getCommentById($idComment);

        if ($comment === null) {
            throw new InvalidArgumentException("Aucun commentaire trouvé avec l'ID : $idComment");
        }

        return $comment;
    }

    /**
     * Modifie un commentaire prédéfini grâce à son ID et au nouveau texte du commentaire
     *
     * @param int $idcomment
     * @param string $textcomment
     * @return bool
     */
    public static function commentUpdate(int $idcomment, string $textcomment): bool
    {
        return CommentUpdater::update($idcomment, $textcomment);
    }

}