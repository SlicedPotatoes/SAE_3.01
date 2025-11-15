<?php

namespace Uphf\GestionAbsence\Model\Validation;

use DateTime;

/**
 * Classe de validation des inputs envoyée en POST pour la création d'un justificatif par l'étudiant
 */
class CreateJustificationValidator {
    private array | null $input;

    public function __construct() {
        /**
         * Vérifie le format de startDate
         *
         * Vérifie le format de endDate
         *
         * Enlève les espaces de début et fin de chaine pour absenceReason
         */
        $this->input = filter_input_array(
            INPUT_POST,
            [
                "startDate" => [
                    "filter" => FILTER_CALLBACK,
                    "options" => function ($val) { return ValidationHelper::validateDate($val, "Y-m-d"); }
                ],
                "endDate" => [
                    "filter" => FILTER_CALLBACK,
                    "options" => function ($val) { return ValidationHelper::validateDate($val, "Y-m-d"); }
                ],
                "absenceReason" => [
                    "filter" => FILTER_CALLBACK,
                    "options" => [ValidationHelper::class, 'stringOrNull']
                ],
            ]
        );
    }

    /**
     * Renvoie une liste d'erreur
     *
     * - Check si les champs startDate, endDate et absenceReason sont présents
     * - Vérifie si startDate est inférieure ou égale à endDate
     *
     * @return array
     */
    public function checkAllGood(): array {
        // Ne devrais pas arriver avec une utilisation normale de l'application
        if(!isset($this->input)) {
            return ["Créer Justification: Impossible de traiter votre demande, veuillez contacter l'administrateur"];
        }

        $errors = ValidationHelper::validateRequired(
            $this->input,
            [
                'startDate',
                'endDate',
                'absenceReason'
            ],
            [
                'Créer Justification: La date de début a un format invalide.',
                'Créer Justification: La date de fin a un format invalide.',
                'Créer Justification: Veuillez fournir une raison.',
            ]
        );

        if(empty($errors) &&
           DateTime::createFromFormat("Y-m-d", $this->input['startDate']) >
           DateTime::createFromFormat("Y-m-d", $this->input['endDate'])
        ) {
            $errors[] = 'Créer Justification: La date de début doit être inférieur ou égal a la date de fin';
        }

        return $errors;
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