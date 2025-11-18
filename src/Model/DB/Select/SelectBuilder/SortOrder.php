<?php

namespace Uphf\GestionAbsence\Model\DB\Select\SelectBuilder;

/**
 * Enumération utilisé dans les différents builder pour les requêtes SELECT
 * Permet de définir si l'ordre est croissant ou décroissant
 */
enum SortOrder: string {
    case ASC = 'ASC';
    case DESC = 'DESC';
}