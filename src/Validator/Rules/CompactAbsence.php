<?php

namespace Uphf\GestionAbsence\Validator\Rules;

use DateTime;
use Respect\Validation\Rules\Core\Simple;

/**
 * Implémentation d'une règle pour validé un string au format "idStudent_datetime"
 * où datetime est au format "YYYY-mm-dd HH:mm:ss"
 */
class CompactAbsence extends Simple {
    public function isValid(mixed $input): bool {
        if(!is_string($input)) {
            return false;
        }

        $parts = explode('_', $input, 2);

        if(count($parts) !== 2) {
            return false;
        }

        [$id, $datetime] = $parts;

        if(!ctype_digit($id)) {
            return false;
        }

        $date = DateTime::createFromFormat('Y-m-d H:i:s', $datetime);

        // createFromFormat retourne false si la date n'est pas au bon format
        // "tente" de corrigé une date invalide donc par exemple "2026-02-31" deviendra "2026-03-03" (2026-02-28 + 3 jours)
        return $date && $date->format("Y-m-d H:i:s") === $datetime;
    }
}