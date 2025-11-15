<?php

namespace Uphf\GestionAbsence\Model\Validation;

use Uphf\GestionAbsence\Model\Entity\Absence\StateAbs;

/**
 * Classe de validation des inputs envoyée en POST pour le traitement d'un justificatif par le RP
 */
class ProcessJustificationValidator {
    private array | null $input;
    public function __construct() {
        /**
         * Filtre du "1er niveau"
         * - rejectionReason => Enlève tout les espaces de début et fin de chaine
         * - absences => Dans le cas ce n'est pas un tableau, transforme en tableau avec une seule valeur.
         */
        $this->input = filter_input_array(
            INPUT_POST,
            [
                "rejectionReason" => [
                    "filter" => FILTER_CALLBACK,
                    "options" => [ValidationHelper::class, 'stringOrNull']
                ],
                "absences" => [
                    "filter" => FILTER_DEFAULT,
                    "flags" => FILTER_FORCE_ARRAY
                ]
            ]
        );

        if(!isset($this->input) || !is_array($this->input['absences'])) {
            return;
        }

        /**
         * Filtre du tableau absences
         *
         * Pour chaque paire key => value d'absences:
         * - value["state"] => transformer en StateAbs
         * - value['lock'] => récupérer le boolean associer
         * - Si value['state'] ou value['lock'] ne son pas valide, la clé est supprimé.
         */
        foreach($this->input['absences'] as $key => $value) {
            $value = filter_var_array(
                $value,
                [
                    "state" => [
                        "filter" => FILTER_CALLBACK,
                        "options" => [StateAbs::class, 'tryFrom']
                    ],
                    "lock" => [
                        "filter" => FILTER_VALIDATE_BOOL,
                        "flags" => FILTER_NULL_ON_FAILURE
                    ]
                ]
            );

            // Si une des deux valeurs pour cette absence ne sont pas correcte, on supprime l'entrée du tableau
            if(!isset($value['state']) || !isset($value['lock'])) {
                unset($this->input['absences'][$key]);
                continue;
            }

            // Si on valide l'absence, elle est forcément lock
            if($value['state'] == StateAbs::Validated) {
                $value['lock'] = true;
            }

            $this->input['absences'][$key] = $value;
        }
    }

    public function checkAllGood(): bool {
        return isset($this->input) && is_array($this->input['absences']);
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