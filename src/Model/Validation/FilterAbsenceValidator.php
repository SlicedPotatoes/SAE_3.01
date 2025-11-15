<?php

namespace Uphf\GestionAbsence\Model\Validation;

use Uphf\GestionAbsence\Model\Entity\Absence\StateAbs;

/**
 * Classe de validation des inputs envoyée en POST les filtres des absences
 */
class FilterAbsenceValidator {
    private array | null $input;

    public function __construct() {
        /**
         * Vérifie le format de dateStart
         *
         * Vérifie le format de endDate
         *
         * Vérifie le format de state
         *
         * Vérifie le format de examen
         *
         * Vérifie le format de lock
         */
        $this->input = filter_input_array(
            INPUT_POST,
            [
                "dateStart" => [
                    "filter" => FILTER_CALLBACK,
                    "options" => function ($val) { return ValidationHelper::validateDate($val, "Y-m-d"); }
                ],
                "dateEnd" => [
                    "filter" => FILTER_CALLBACK,
                    "options" => function ($val) { return ValidationHelper::validateDate($val, "Y-m-d"); }
                ],
                "state" => [
                    "filter" => FILTER_CALLBACK,
                    "options" => [StateAbs::class, "tryFrom"]
                ],
                "examen" => [
                    "filter" => FILTER_VALIDATE_BOOL,
                    "flags" => FILTER_NULL_ON_FAILURE
                ],
                "lock" => [
                    "filter" => FILTER_VALIDATE_BOOL,
                    "flags" => FILTER_NULL_ON_FAILURE
                ]
            ]
        );
    }

    /**
     * Récupérer les données filtrées
     *
     * @return array
     */
    public function getData(): array {
        return $this->input ?? [];
    }
}