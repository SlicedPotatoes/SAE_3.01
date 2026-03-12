<?php

namespace Uphf\GestionAbsence\Model\Entity\Comment;

use DateTime;
use stdClass;

/**
 * Classe Comment représentant un commentaire prédéfini
 */
class Comment
{
    private int $idComment;
    private string $textComment;

    public function __construct(int $idComment, string $textComment)
    {
        $this->idComment = $idComment;
        $this->textComment = $textComment;
    }

    public function getIdComment(): int
    {
        return $this->idComment;
    }

    public function getTextComment(): string
    {
        return $this->textComment;
    }

    public function jsonSerialize(){
        $json = new stdClass();
        $json->idComment = $this->idComment;
        $json->textComment = $this->textComment;
        return $json;
    }

    public static function jsonSerializeComment(array $Comments){
        $result = [];
        foreach($Comments as $Comment){
            $result[] = $Comment->jsonSerialize();
        }
        return $result;
    }

}
