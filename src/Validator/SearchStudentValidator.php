<?php

namespace Uphf\GestionAbsence\Validator;

use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

/**
 * Classe responsable de la validation des entrées utilisateurs du controller SearchStudentControllerApi
 */
class SearchStudentValidator {
    /**
     * Renvoie une exception, si les données ne contiennent pas :
     *
     * - Une clé "search" (optionnel), avec pour valeur null ou un string
     * - Une clé "groupStudent" (optionnel), avec pour valeur null ou un int
     *
     * Dans le cas où une clé n'existe pas, elle est créer avec pour valeur null
     * Dans le cas où search est empty, le remplace par null
     *
     * @param array $data
     * @return void
     * @throws NestedValidationException
     */
    public static function validationPostPredefinedComment(array &$data): void {
        $validator = v::key('search',
            v::oneOf(v::nullType(), v::stringType()),
            false
        )->key('groupStudent',
            v::oneOf(v::nullType(), v::intVal()),
            false
        );

        $validator->assert($data);

        // On créer les clés avec comme valeurs null dans le cas ou elle n'existe pas
        if(!isset($data['search'])) {
            $data['search'] = null;
        }
        if(!isset($data['groupStudent'])) {
            $data['groupStudent'] = null;
        }

        // Remplace le string vide par null
        if($data['search'] === '') {
            $data['search'] = null;
        }
    }
}