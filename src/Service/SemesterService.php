<?php

namespace Uphf\GestionAbsence\Service;

use Uphf\GestionAbsence\Database\Select\SemesterSelector;
use Uphf\GestionAbsence\Database\Update\SemesterUpdater;
use Uphf\GestionAbsence\Exception\EntityNotFoundException;
use Uphf\GestionAbsence\Model\Entity\Semester;

class SemesterService
{
    /**
     * Récupérer tous les semestres d'une année universitaire
     *
     * @param int $idAcademicYear
     * @return Semester[]
     */
    public static function getByAcademicYear(int $idAcademicYear): array
    {
        return SemesterSelector::getByAcademicYear($idAcademicYear);
    }

    /**
     * Récupérer les semestres de l'année universitaire actuelle
     *
     * @return Semester[]
     */
    public static function getCurrentSemesters(): array
    {
        return SemesterSelector::getCurrentSemesters();
    }

    /**
     * Récupérer un semestre par son ID
     *
     * @param int $id
     * @return Semester
     * @throws EntityNotFoundException Si le semestre n'existe pas
     */
    public static function getById(int $id): Semester
    {
        $semester = SemesterSelector::getById($id);

        if ($semester === null) {
            throw new EntityNotFoundException("Aucun semestre trouvé avec l'ID : $id");
        }

        return $semester;
    }

    /**
     * Mettre à jour les dates d'un semestre
     *
     * @param int $id
     * @param string $startDate
     * @param string $endDate
     * @return bool
     */
    public static function update(int $id, string $startDate, string $endDate): bool
    {
        return SemesterUpdater::update($id, $startDate, $endDate);
    }
}
