<?php

namespace Uphf\GestionAbsence\Model\Statistics;

/**
 * Classe statique permettant d'adapter les données facilement en fonction du graphique
 */
class DataAdapter {
    /**
     * Prend les données de la bdd et les mets dans un format exploitable par chartJS
     *
     * @param array $rows : Données de la BDD
     * @param callable|null $formatterLabel : fonction permettant d'appliqué un traitement spécifique aux label
     * @param callable|null $bgColor : fonction permettant d'ajouter des couleurs spécifique en fonction du label
     * @return array
     */
    public static function proportionAdapter(array $rows, ?callable $formatterLabel, ?callable $bgColor): array {
        $formatterLabel ??= [self::class, 'defaultCallable'];

        $labels = [];
        $data = [];
        $backgroundColor = isset($bgColor) ? [] : null;

        foreach ($rows as $row) {
            $labels[] = $formatterLabel($row['label']);
            $data[] = $row['value'];
            if(isset($backgroundColor)) {
                $backgroundColor[] = $bgColor($row['label']);
            }
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'backgroundColor' => $backgroundColor
        ];
    }

    /**
     * Formatter par défault (aucun traitement)
     * @param $v
     * @return mixed
     */
    private static function defaultCallable($v) {
        return $v;
    }
}