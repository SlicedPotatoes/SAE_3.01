<?php

namespace Uphf\GestionAbsence\ViewModel;

use Uphf\GestionAbsence\Model\Entity\Account\AccountType;

readonly class PredefinedCommentViewModel extends BaseViewModel{

    public array $comments;
    public AccountType $roleUser;

    public function __construct(
        AccountType $roleUser,
        array $comments,
    ){
        $this->roleUser = $roleUser;
        $this->comments = array_map(fn($comment) => [
            'idComment' => $comment->getIdComment(),
            'label' => $comment->getTextComment()
        ], $comments);
    }


}