<?php

namespace Uphf\GestionAbsence\Service;

use Uphf\GestionAbsence\Database\Select\SelectBuilder\AbsenceSelectBuilder;
use Uphf\GestionAbsence\Database\Select\SelectBuilder\SortOrder;

/**
 * Service pour les absences
 */
class AbsenceService
{
    /**
     * Cette méthode permet de récupérer les absences d'un étudiant en fonction de différents filtres.
     *
     * @param int $idStudent
     * @param array $filters
     * @return array
     */
    public static function absenceSelectService(int $idStudent, array $filters): array
    {
        $builder = new AbsenceSelectBuilder()->idStudent($idStudent);

        $whiteListMethod = ['dateStart', 'dateEnd', 'state', 'examen', 'lock'];

        foreach ($filters as $filter => $value) {
            if(isset($value) && in_array($filter, $whiteListMethod)) {
                call_user_func([$builder, $filter], $value);
            }
        }

        $builder->orderBy(["time"], SortOrder::DESC);

        return $builder->execute();
    }
}