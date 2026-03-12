<?php

namespace Uphf\GestionAbsence\Validator;

use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

class AuthentificationValidator {
    /**
     * Renvoie une exception, si les données ne contiennent pas :
     *
     * - Une clé "email" avec une valeur non vide
     * - Une clé "password" avec une valeur non vide
     *
     * @param $data
     * @return void
     * @throws NestedValidationException
     */
    public static function validateLogin($data): void {
        $validator =
            v::key('email',
                v::stringType()
                    ->notBlank()
                    ->setTemplate("L'email est obligatoire")
            )
            ->key('password',
                v::stringType()
                    ->notEmpty()
                    ->setTemplate("Le mot de passe est obligatoire")
            );

        $validator->assert($data);
    }
}