<?php

namespace Uphf\GestionAbsence\Model\Validation;

use DateTime;

/**
 * Classe de validation des inputs envoyée en POST pour la création d'un justificatif par l'étudiant
 */
class ChangePasswordValidator {
    private array | null $input;

    public function __construct() {
        /**
         * Enlève les espaces de début et fin de chaine pour lastPassword, inputNewMDP et inputConfirmMDP
         */
        $this->input = filter_input_array(
            INPUT_POST,
            [
                "lastPassword" => [
                    "filter" => FILTER_CALLBACK,
                    "options" => [ValidationHelper::class, 'stringOrNull']
                ],
                "newPassword" => [
                    "filter" => FILTER_CALLBACK,
                    "options" => [ValidationHelper::class, 'validatePassword']
                ],
                "confirmPassword" => [
                    "filter" => FILTER_CALLBACK,
                    "options" => [ValidationHelper::class, 'stringOrNull']
                ],
            ]
        );
    }

    /**
     * Renvoie une liste d'erreur
     *
     * - Check si les champs inputNewMDP et inputConfirmMDP sont présents
     * - Vérifie si inputNewMDP et inputConfirmMDP sont egaux
     *
     * @return array
     */
    public function checkAllGood(): array {
        // Ne devrais pas arriver avec une utilisation normale de l'application
        if(!isset($this->input)) {
            return ["Changement de mot de passe: Impossible de traiter votre demande, veuillez contacter l'administrateur"];
        }

        if(!isset($this->input['newPassword']) || !isset($this->input['confirmPassword'])) {
            return ["Changement de mot de passe: Votre nouveau de mot de passe n'est pas au bon format."];
        }

        if(empty($errors) && $this->input['newPassword'] != $this->input['confirmPassword']) {
            return ['Changement de mot de passe: Les deux mots de passe sont différents'];
        }

        return [];
    }

    /**
     * Récupérer les données filtrées
     *
     * @return array | null
     */
    public function getData(): array | null {
        return $this->input;
    }
}