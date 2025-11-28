<?php

namespace Uphf\GestionAbsence\Model\Hydrator;

use Uphf\GestionAbsence\Model\Entity\Comment\Comment;

/**
 * Hydrator pour les commentaires prédéfinis
 * Permet de transformer les données brutes de la BDD en objets Comment
 */
class CommentHydrator
{
    /**
     * Récupère un objet Comment à partir de données brutes
     *
     * @param array $raw Données brutes de la BDD (doit contenir 'idcomment' et 'textcomment')
     * @return Comment
     */
    public static function unserializeComment(array $raw): Comment
    {
        return new Comment(
            $raw['idcomment'],
            $raw['textcomment']
        );
    }
}
