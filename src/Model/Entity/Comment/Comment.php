<?php

namespace Uphf\GestionAbsence\Model\Entity\Comment;

use JsonSerializable;

/**
 * Classe Comment représentant un commentaire prédéfini
 */
class Comment implements JsonSerializable
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

    public function jsonSerialize(): array {
        return [
            'idComment' => $this->idComment,
            'textComment' => $this->textComment,
        ];
    }
}
