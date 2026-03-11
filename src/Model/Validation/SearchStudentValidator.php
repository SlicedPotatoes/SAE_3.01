<?php

namespace Uphf\GestionAbsence\Model\Validation;

class SearchStudentValidator {
    private array | null $input;

    public function __construct(array $data) {
        $this->input = filter_var_array(
            $data,
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

        if (empty($this->input["search"])) {
            $this->input["search"] = null;
        }
        if (empty($this->input["groupStudent"])) {
            $this->input["groupStudent"] = null;
        }
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