<?php

namespace Uphf\GestionAbsence\Service;

use DateTime;
use InvalidArgumentException;
use Uphf\GestionAbsence\Database\Insert\OffPeriodInsertor;
use Uphf\GestionAbsence\Database\Select\OffPeriodSelector;
use Uphf\GestionAbsence\Database\Update\OffPeriodUpdater;
use Uphf\GestionAbsence\Model\Entity\OffPeriod;

/**
 * Service responsable de la gestion des périodes de vacances
 *
 * CRUD
 */
class HolidaysService {

    /**
     * Insérer une période de vacances
     *
     * @param string $start
     * @param string $end
     * @param string $name
     * @return void
     * @throws InvalidArgumentException Dans le cas ou la date de début est supérieure à la date de fin
     */
    public static function insert (string $start, string $end, string $name): void {
        if(DateTime::createFromFormat("Y-m-d", $end) < DateTime::createFromFormat("Y-m-d", $start)) {
            throw new InvalidArgumentException("La date de début dois être inférieure a la date de fin");
        }

        OffPeriodInsertor::insert($start, $end, $name);
    }

    /**
     * Supprimer une période de vacances par son ID
     *
     * @param int $id
     * @return void
     */
    public static function delete (int $id): void {
        OffPeriodUpdater::delete($id);
    }

    /**
     * Mettre à jour une période de vacances
     *
     * @param int $id
     * @param string $start
     * @param string $end
     * @param string $name
     * @return void
     * @throws InvalidArgumentException Dans le cas ou la date de début est supérieure à la date de fin
     */
    public static function update (int $id, string $start, string $end, string $name): void {
        if(DateTime::createFromFormat("Y-m-d", $end) < DateTime::createFromFormat("Y-m-d", $start)) {
            throw new InvalidArgumentException("La date de début dois être inférieure a la date de fin");
        }

        OffPeriodUpdater::update($id, $start, $end, $name);
    }

    /**
     * Récupérer l'intégralité des périodes de vacances
     *
     * @return OffPeriod[]
     */
    public static function selectAll (): array {
        return OffPeriodSelector::getOffPeriod();
    }
}