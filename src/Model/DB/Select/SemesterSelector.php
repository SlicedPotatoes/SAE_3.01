<?php

namespace Uphf\GestionAbsence\Model\DB\Select;

use Uphf\GestionAbsence\Model\DB\Connection;
use Uphf\GestionAbsence\Model\Entity\Semester;
use Uphf\GestionAbsence\Model\Hydrator\SemesterHydrator;
use PDO;

/**
 * Classe static pour sélectionner les semestres depuis la BDD
 */
class SemesterSelector {

    /**
     * Récupérer tous les semestres d'une année universitaire
     *
     * @param int $idAcademicYear
     * @return Semester[]
     */
    public static function getByAcademicYear(int $idAcademicYear): array {
        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare('SELECT * FROM semester WHERE idAcademicYear = :idAcademicYear ORDER BY startDate');
        $stmt->execute(['idAcademicYear' => $idAcademicYear]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $res = [];
        foreach ($rows as $row) {
            $res[] = SemesterHydrator::unserialize($row);
        }

        return $res;
    }

    /**
     * Récupérer les semestres de l'année universitaire actuelle (la plus récente)
     *
     * @return Semester[]
     */
    public static function getCurrentSemesters(): array {
        $pdo = Connection::getInstance();
        $stmt = $pdo->query('
            SELECT s.* FROM semester s
            JOIN academicYear a ON s.idAcademicYear = a.id
            ORDER BY a.id DESC, s.startDate ASC
            LIMIT 2
        ');
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $res = [];
        foreach ($rows as $row) {
            $res[] = SemesterHydrator::unserialize($row);
        }

        return $res;
    }

    /**
     * Récupérer un semestre par son ID
     *
     * @param int $id
     * @return Semester|null
     */
    public static function getById(int $id): ?Semester {
        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare('SELECT * FROM semester WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return SemesterHydrator::unserialize($row);
    }
}
