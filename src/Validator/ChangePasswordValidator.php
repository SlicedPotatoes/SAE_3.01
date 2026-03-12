<?php

namespace Uphf\GestionAbsence\Validator;

use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validatable;
use Respect\Validation\Validator as v;
use Uphf\GestionAbsence\Exception\InvalidPasswordConfirmationException;

/**
 * Classe responsable de la validation des entrées utilisateurs du controller ChangePasswordController
 */
class ChangePasswordValidator {

    /**
     * Récupérer les règles de validations d'un mot de passe
     *
     * @return Validatable
     */
    private static function getPasswordRule(): Validatable {
        return v::stringType()
            ->length(12, 30)->setTemplate("Le mot de passe doit contenir entre 12 et 30 caractères")
            ->regex('/[A-Z]/')->setTemplate("Le mot de passe doit contenir une majuscule")
            ->regex('/[a-z]/')->setTemplate("Le mot de passe doit contenir une minuscule")
            ->regex('/[0-9]/')->setTemplate("Le mot de passe doit contenir un chiffre")
            ->regex('/[^0-9A-Za-zÀ-ÖØ-öø-ÿ]/')->setTemplate("Le mot de passe doit contenir un caractère spécial")
            ->not(v::regex('/\s/'))->setTemplate("Le mot de passe ne doit pas contenir d'espace");
    }

    /**
     * Renvoie une exception, si les données ne contiennent pas :
     *
     * - Une clé "lastPassword", de type string, non vide
     * - Une clé "newPassword", de type string, respectant les règles suivantes :
     *      - Une longeur comprise entre 12 et 30 caractères
     *      - Contient une majuscule
     *      - Contient une minuscule
     *      - Contient un chiffre
     *      - Contient un caractère spécial
     *      - Ne contient pas d'espace
     * - Une clé "confirmPassword", de type string, non vide, identique à "newPassword"
     *
     * @param $data
     * @return void
     * @throws NestedValidationException
     * @throws InvalidPasswordConfirmationException
     */
    public static function validateConnectedChangePassword($data): void {
        $validator =
            v::key('lastPassword', v::stringType()
                ->notEmpty()->setTemplate("L'ancien mot de passe n'a pas été fournis")
            )->setTemplate("Le champ 'Ancien mot de passe' est obligatoire")
            ->key('newPassword', self::getPasswordRule())->setTemplate("Le champ 'Nouveau mot de passe' est obligatoire")
            ->key('confirmPassword', v::stringType()
                ->notEmpty()->setTemplate("Veuillez confirmer votre nouveau mot de passe")
            )->setTemplate("Le champ 'Confirmer le nouveau mot de passe' est obligatoire");

        $validator->assert($data);

        if($data['newPassword'] !== $data['confirmPassword']) {
            throw new InvalidPasswordConfirmationException("Les mots de passe de confirmation ne correspondent pas au nouveau mot de passe");
        }
    }

    /**
     * Renvoie une exception, si les données ne contiennent pas :
     *
     * - Une clé "email", de type string, non vide
     *
     * @param $data
     * @return void
     * @throws NestedValidationException
     */
    public static function validatePasswordLost($data): void {
        $validator = v::key('email', v::stringType()
            ->notEmpty()->setTemplate("L'email est obligatoire")
        )->setTemplate("Le champ 'Adresse e-mail' est obligatoire");

        $validator->assert($data);
    }

    /**
     * Renvoie une exception, si les données ne contiennent pas :
     *
     * - Une clé "newPassword", de type string, respectant les règles suivantes :
     *      - Une longeur comprise entre 12 et 30 caractères
     *      - Contient une majuscule
     *      - Contient une minuscule
     *      - Contient un chiffre
     *      - Contient un caractère spécial
     *      - Ne contient pas d'espace
     * - Une clé "confirmPassword", de type string, non vide, identique à "newPassword"
     *
     * @param $data
     * @return void
     * @throws NestedValidationException
     * @throws InvalidPasswordConfirmationException
     */
    public static function validateTokenChangePassword($data): void {
        $validator =
            v::key('newPassword', self::getPasswordRule())->setTemplate("Le champ 'Nouveau mot de passe' est obligatoire")
            ->key('confirmPassword', v::stringType()
                ->notEmpty()->setTemplate("Veuillez confirmer votre nouveau mot de passe")
            )->setTemplate("Le champ 'Confirmer le nouveau mot de passe' est obligatoire");

        $validator->assert($data);
    }
}