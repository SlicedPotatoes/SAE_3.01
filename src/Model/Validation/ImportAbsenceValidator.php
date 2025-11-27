<?php

namespace Uphf\GestionAbsence\Model\Validation;

use Uphf\GestionAbsence\Model\Entity\Absence\CourseType;

/**
 * Classe de validation des lignes d'un export VT pour les absences
 */
class ImportAbsenceValidator {
    private static array $column = ['Identifiant', 'Date', 'Heure', 'Durée', 'Type', 'Matière', 'Absent/Présent', 'Profs', 'Contrôle'];
    private array $input;

    /**
     * Pour chaque ligne:
     *
     * - Identifiant doit être un INT
     * - Date doit avoir le format d/m/Y
     * - Heure doit avoir le format G:i
     * - Durée doit avoir le format G:i
     * - Type dois être un CourseType valide
     * - Matière ne peut pas être null
     * - Absent/Présent, peut ne rien avoir (traité ailleurs)
     * - Profs, peut être null
     * - Contrôle doit avoir la valeur "Oui" ou "Non"
     *
     * Si un critère n'est pas respecté pour la ligne, celle-ci est supprimé
     */
    public function __construct(array $arr) {
        $this->input = $arr;

        foreach($this->input as $key => $value) {
            $value = filter_var_array(
                $value,
                [
                    "Identifiant" => [
                        "filter" => FILTER_VALIDATE_INT,
                        "flags" => FILTER_NULL_ON_FAILURE
                    ],
                    "Date" => [
                        "filter" => FILTER_CALLBACK,
                        "options" => function ($val) { return ValidationHelper::validateDate($val, "d/m/Y"); }
                    ],
                    "Heure" => [
                        "filter" => FILTER_CALLBACK,
                        "options" => [ValidationHelper::class, 'valideHours']
                    ],
                    "Durée" => [
                        "filter" => FILTER_CALLBACK,
                        "options" => [ValidationHelper::class, 'valideHours']
                    ],
                    "Type" => [
                        "filter" => FILTER_CALLBACK,
                        "options" => [CourseType::class, 'tryFrom']
                    ],
                    "Matière" => [
                        "filter" => FILTER_CALLBACK,
                        "options" => [ValidationHelper::class, 'stringOrNull']
                    ],
                    "Absent/Présent" => [
                        "filter" => FILTER_DEFAULT
                    ],
                    "Profs" => [
                        "filter" => FILTER_DEFAULT
                    ],
                    "Contrôle" => [
                        "filter" => FILTER_CALLBACK,
                        "options" => function ($val) { if($val === "Oui" || $val === "Non") { return $val; } return null; }
                    ]
                ]
            );

            if(!$this->checkLine($value)) {
                unset($this->input[$key]);
                continue;
            }

            $this->input[$key] = $value;
        }
    }

    /**
     * Prend une ligne et vérifie si les filtres n'ont pas renvoyé "null" comme valeur pour une column
     *
     * Renvoie vrai si aucun null, sinon false
     *
     * @param $l
     * @return bool
     */
    private function checkLine($l): bool {
        foreach(self::$column as $c) {
            if(!isset($l[$c])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Renvoie les données filtrées
     *
     * @return array
     */
    public function getData(): array {
        return $this->input;
    }
}