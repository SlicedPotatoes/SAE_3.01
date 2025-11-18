<?php
namespace Uphf\GestionAbsence\Model\Entity\Account;

use Uphf\GestionAbsence\Model\DB\Select\TableSelector;
use Uphf\GestionAbsence\Model\Hydrator\AccountHydrator;

/**
 * Représente les groupes des étudiants, basé sur la base de données.
 */
class GroupStudent {
    private int $idGroupStudent;
    private string $label;

    function __construct($idGroupStudent, $label) {
        $this->idGroupStudent = $idGroupStudent;
        $this->label = $label;
    }

    // Getter de base
    public function getIdGroupStudent(): int { return $this->idGroupStudent; }
    public function getLabel(): string { return $this->label; }

    /**
     * Récupère dans la base de données les groupes d'étudiant
     * @return GroupStudent[]
     */
    public static function getAllGroupsStudent(): array {
        $res = TableSelector::fromTable("GroupStudent");

        // TODO: rustine, à enlever
        //$res = array_map(fn($g) => [ "groupid" => $g["idgroupstudent"], "grouplabel" => $g["label"] ], $res);

        $groupStudent = [];

        foreach ($res as $r) {
            $groupStudent[] = AccountHydrator::unserializeGroupStudent($r);
        }

        return $groupStudent;
    }
}