<?php

namespace Uphf\GestionAbsence\Database\Select\SelectBuilder;

/**
 * Enumération utilisé dans les différents builder pour les requêtes SELECT
 * Permet de définir si l'ordre est croissant ou décroissant
 */
enum SortOrder: string {
    case ASC = 'ASC';
    case DESC = 'DESC';
}