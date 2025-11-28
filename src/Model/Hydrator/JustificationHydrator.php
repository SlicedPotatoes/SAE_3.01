<?php

namespace Uphf\GestionAbsence\Model\Hydrator;

use Uphf\GestionAbsence\Model\Entity\Justification\File;
use Uphf\GestionAbsence\Model\Entity\Justification\Justification;
use Uphf\GestionAbsence\Model\Entity\Justification\StateJustif;

use DateTime;

/**
 * Hydrator pattern, récupérer une entité plus ou moins proche de Justification:
 * - Justification
 * - File
 *
 * Elles permettent à partir de données brutes de récupérer un objet
 */
class JustificationHydrator {

    /**
     * Récupérer un objet Justification à partir de données brutes
     *
     * @param array $raw
     * @return Justification
     */
    public static function unserializeJustification(array $raw): Justification {
        return new Justification(
            $raw['idjustification'],
            $raw['cause'],
            StateJustif::from($raw['currentstate']),
            DateTime::createFromFormat("Y-m-d H:i:s", $raw['startdate']),
            DateTime::createFromFormat("Y-m-d H:i:s", $raw['enddate']),
            DateTime::createFromFormat("Y-m-d H:i:s.u", $raw['senddate']),
            $raw['idcomments'] ?? 0,
            isset($raw['processeddate']) ? DateTime::createFromFormat("Y-m-d H:i:s.u", $raw['processeddate']) : null,
            $raw['refusalreason'] ?? null,
            AccountHydrator::unserializeStudent($raw)
        );
    }

    public static function unserializeFile(array $raw): File {
        return new File(
            $raw["idfile"],
            $raw["filename"],
            $raw["idjustification"]
        );
    }
}