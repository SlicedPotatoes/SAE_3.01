<?php

namespace Uphf\GestionAbsence\Model\DB\Select\SelectBuilder;

use Uphf\GestionAbsence\Model\Entity\Absence\StateAbs;

/**
 * Enumération représentant les différents types de requête
 * pour les statistiques par proportions
 */
enum ProportionStatisticsType: string {
    case TypeCourse = 'typeCourse';
    case Teacher = 'teacher';
    case Resource = 'resource';
    case Group = 'group';
    case State = 'state';
    case Examen = 'examen';

    /**
     * Colonne "label", afficher dans le graphique
     * @return string
     */
    public function select(): string {
        return match($this) {
            self::TypeCourse => "a.coursetype",
            self::Teacher => "COALESCE(t.lastname || ' ' || t.firstname, 'Autonomie')",
            self::Resource => "r.label",
            self::Group => "g.grouplabel",
            self::State => "a.currentstate",
            self::Examen => "a.examen"
        };
    }

    /**
     * Jointure pour la requête
     * @return string
     */
    public function join(): string {
        return match($this) {
            self::TypeCourse, self::State, self::Examen =>
                "",
            self::Teacher =>
                "LEFT JOIN account t ON a.idteacher = t.idaccount",
            self::Resource =>
                "JOIN resource r USING(idresource)",
            self::Group =>
                "JOIN student s ON a.idstudent = s.idaccount
                 JOIN groupstudent g ON g.groupid = s.idgroupstudent"
        };
    }

    /**
     * Groupby pour la requête
     * @return string
     */
    public function groupby(): string {
        return match($this) {
            self::TypeCourse => "a.coursetype",
            self::Teacher => "t.idaccount",
            self::Resource => "r.idresource",
            self::Group => "g.groupid",
            self::State => "a.currentstate",
            self::Examen => "a.examen"
        };
    }

    /**
     * Titre long, utilisé dans le tooltip des tabs et le titre du graphique
     * @return string
     */
    public function title(): string {
        return match($this) {
            self::TypeCourse => "Proportion par type de cours",
            self::Teacher => "Proportion par enseignant",
            self::Resource => "Proportion par ressource",
            self::Group => "Proportion par groupe",
            self::State => "Proportion par état",
            self::Examen => "Proportion examen"
        };
    }

    /**
     * Titre court, pour les tabs
     * @return string
     */
    public function shortTitle(): string {
        return match($this) {
            self::TypeCourse => "P. type de cours",
            self::Teacher => "P. enseignant",
            self::Resource => "P. ressource",
            self::Group => "P. groupe",
            self::State => "P. état",
            self::Examen => "P. examen"
        };
    }

    /**
     * Fonction de traitement des labels selon le type de chart
     * @return callable|null
     */
    public function callableLabelFormat(): ?callable {
        return match($this) {
            self::State => function($v) {
                return StateAbs::from($v)->label();
            },
            self::Examen => function($v) {
                return $v ? 'Avec examen' : 'Sans examen';
            },
            default => null
        };
    }

    /**
     * Fonction pour coloré en fonction du label
     * @return callable|null
     */
    public function callableColorPie(): ?callable {
        return match($this) {
            self::State => function($v) {
                return match(StateAbs::from($v)) {
                    StateAbs::Validated => 'rgb(25, 135, 84)',
                    StateAbs::Refused => 'rgb(220, 53, 69)',
                    StateAbs::NotJustified => 'rgb(255, 38, 53)',
                    StateAbs::Pending => 'rgb(108, 117, 125)'
                };
            },
            self::Examen => function ($v) {
                return $v ? 'rgb(255, 205, 86)' : 'rgb(54, 162, 235)';
            },
            default => null
        };
    }

    /**
     * Récupérer l'ensemble des types de chart
     * @return ProportionStatisticsType[]
     */
    public static function getAll(): array {
        return [
            self::TypeCourse,
            self::Teacher,
            self::Resource,
            self::Group,
            self::State,
            self::Examen
        ];
    }
}