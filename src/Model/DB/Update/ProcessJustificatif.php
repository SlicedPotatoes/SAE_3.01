<?php

namespace Uphf\GestionAbsence\Model\DB\Update;

use Uphf\GestionAbsence\Model\DB\Connection;
use Uphf\GestionAbsence\Model\Entity\Justification\Justification;
use Uphf\GestionAbsence\Model\Entity\Justification\StateJustif;
use BadMethodCallException;
use PDO;

/**
 * Classe permettant de traiter un justificatif
 */
class ProcessJustificatif {

    /**
     * Traiter un justificatif
     *
     * @param Justification $justification
     * @param string $comment Commentaire du RP
     * @return void
     * @throws BadMethodCallException Dans le cas où le justificatif est déjà traité
     */
    public static function execute(Justification $justification, string $comment): void {
        // Si le justificatif est déjà traité, on ne fait rien
        if($justification->getCurrentState() == StateJustif::Processed) {
            throw new BadMethodCallException("Le justificatif est déjà traité");
        }

        $conn = Connection::getInstance();

        $query = "UPDATE Justification
                  SET currentState = 'Processed',
                      processedDate = now(),
                      refusalReason = :comment
                  WHERE idJustification = :idJustification";

        $sql = $conn->prepare($query);

        $sql->bindValue(":comment", $comment, PDO::PARAM_STR);
        $sql->bindValue(":idJustification", $justification->getIdJustification(), PDO::PARAM_INT);
        $sql->execute();

        $justification->setRefusalReason($comment);
        $justification->setState(StateJustif::Processed);
        $justification->setProcessedDate();
    }
}

/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . "/../../../../vendor/autoload.php";

use Uphf\GestionAbsence\Model\DB\Select\JustificationSelector;

ProcessJustificatif::execute(JustificationSelector::getJustificationById(85), "Banane");
*/