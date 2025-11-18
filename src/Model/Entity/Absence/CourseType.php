<?php

namespace Uphf\GestionAbsence\Model\Entity\Absence;

/**
 * Énumération représentant les types de cours
 */
enum CourseType: string {
    case CM = "CM";
    case TD = "TD";
    case TP = "TP";
    case BEN = "BEN";
}