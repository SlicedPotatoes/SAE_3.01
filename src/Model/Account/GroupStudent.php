<?php
namespace Uphf\GestionAbsence\Model\Account;

use Uphf\GestionAbsence\Model\Connection;

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
        $connection = Connection::getInstance();

        $query = "SELECT * FROM GroupStudent";

        $req = $connection->prepare($query);
        $req->execute();

        $res = $req->fetchAll();

        $groupStudent = [];

        foreach ($res as $r) {
            $groupStudent[] = new GroupStudent(
                $r['idgroupstudent'],
                $r['label']
            );
        }

        return $groupStudent;
    }
}