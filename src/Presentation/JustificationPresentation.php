<?php
namespace Uphf\GestionAbsence\Presentation;

/**
 * Classe JustificationPresentation, permettant de faire la liaison entre Model et View pour tout ce qui concerne les Justificatifs
 */

use Uphf\GestionAbsence\Model\Filter\FilterJustification;
use Uphf\GestionAbsence\Model\Justification\Justification;

class JustificationPresentation
{
    /**
     * Récupération d'une liste de justificatifs, en fonction du contexte dans lequel ils sont demandées
     * @param FilterJustification $filter
     * @return Justification[]
     */
    public static function getJustifications(FilterJustification $filter): array
    {
        return Justification::selectJustification(StudentPresentation::getStudentAccountDashboard()->getIdAccount(), $filter);
    }
    public static function getAllJustifications(FilterJustification $filter): array
    {
        return Justification::selectJustification(null, $filter);
    }
    public static function getJustificationById($id) {
        return Justification::getJustificationById($id);
    }
}