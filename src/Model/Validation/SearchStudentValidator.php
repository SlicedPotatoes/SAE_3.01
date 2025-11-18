<?php

namespace Uphf\GestionAbsence\Model\Validation;

class SearchStudentValidator {
    private array | null $input;

    public function __construct() {
        $this->input = filter_input_array(
            INPUT_POST,
            [
                "search" => [
                    "filter" => FILTER_CALLBACK,
                    "options" => [ValidationHelper::class, 'stringOrNull']
                ],
                "groupStudent" => [
                    "filter" => FILTER_VALIDATE_INT,
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