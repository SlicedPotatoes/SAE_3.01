<?php

namespace Uphf\GestionAbsence\Model\Validation;

class CommentValidator
{
    private ?array $input = null;

    public function __construct()
    {
        $this->input = filter_input_array(INPUT_POST,
            [
                "action" => FILTER_SANITIZE_SPECIAL_CHARS,
                "idComment" => [
                    "filter" => FILTER_VALIDATE_INT,
                    "flags" => FILTER_NULL_ON_FAILURE
                ],
                "textComment" => [
                    "filter" => FILTER_CALLBACK,
                    "options" => [ValidationHelper::class, 'stringOrNull']
               ]
            ]
        );
    }

}
