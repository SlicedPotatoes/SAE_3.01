<?php

namespace Uphf\GestionAbsence\Validator;

use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

/**
 * Classe responsable de la validation des entrées utilisateurs du controller PredefinedCommentControllerApi
 */
class PredefinedCommentValidator {

    /**
     * Renvoie une exception, si les données ne contiennent pas :
     *
     * - Une clé "textComment", avec pour valeur un string non null
     *
     * @param array $data
     * @return void
     * @throws NestedValidationException
     */
    public static function validationPostPredefinedComment(array $data): void {
        $validator = v::key('textComment', v::stringType()->notEmpty());

        $validator->assert($data);
    }
}