<?php

namespace Uphf\GestionAbsence\Model\DB\Select\SelectBuilder;

/**
 * Enumération représentant les différents types de requête
 */
enum ProportionStatisticsType {
    case TypeCourse;
    case Teacher;
    case Resource;
    case Group;
    case State;
    case Examen;

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
}