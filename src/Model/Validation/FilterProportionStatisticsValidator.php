<?php

namespace Uphf\GestionAbsence\Model\Validation;

use Uphf\GestionAbsence\Model\Entity\Absence\StateAbs;

class FilterProportionStatisticsValidator {
    private array | null $input;

    public function __construct() {
        /**
         * Vérifier le format de group
         *
         * Vérifier le format d'examen
         *
         * Vérifier le format d'idStudent
         */
        $this->input = filter_input_array(
            INPUT_POST,
            [
                "group" => [
                    "filter" => FILTER_VALIDATE_INT,
                    "options" => [
                        "default" => null
                    ]
                ],
                "examen" => [
                    "filter" => FILTER_VALIDATE_BOOL,
                    "options" => FILTER_NULL_ON_FAILURE
                ],
                "state" => [
                    "filter" => FILTER_CALLBACK,
                    "options" => [StateAbs::class, "tryFrom"]
                ],
                "idStudent" => [
                    "filter" => FILTER_VALIDATE_INT,
                    "options" => FILTER_NULL_ON_FAILURE
                ]
            ]
        );
    }

    public function getData(): array {
        return $this->input ?? [];
    }
}