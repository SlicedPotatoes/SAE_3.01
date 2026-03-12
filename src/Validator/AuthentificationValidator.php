<?php

namespace Uphf\GestionAbsence\Validator;

use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

/**
 * Classe responsable de la validation des entrées utilisateurs du controller AuthentificationController
 */
class AuthentificationValidator {
    /**
     * Renvoie une exception, si les données ne contiennent pas :
     *
     * - Une clé "email", de type string, non vide
     * - Une clé "password" de type string, non vide
     *
     * @param $data
     * @return void
     * @throws NestedValidationException
     */
    public static function validateLogin($data): void {
        $validator =
            v::key('email', v::stringType()
                ->notEmpty()->setTemplate("L'email est obligatoire")
            )->setTemplate("Le champ 'Adresse e-mail' est obligatoire")
            ->key('password', v::stringType()
                ->notEmpty()->setTemplate("Le mot de passe est obligatoire")
            )->setTemplate("Le champ 'Mot de passe' est obligatoire");

        $validator->assert($data);
    }
}