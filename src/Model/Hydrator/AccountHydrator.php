<?php

namespace Uphf\GestionAbsence\Model\Hydrator;

use Uphf\GestionAbsence\Model\Entity\Account\Account;
use Uphf\GestionAbsence\Model\Entity\Account\Student;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\Model\Entity\Account\GroupStudent;
use Uphf\GestionAbsence\Model\Entity\Account\Teacher;

/**
 * Hydrator pattern, récupérer une entité plus ou moins proche de Account:
 * - Account
 * - Student
 * - Teacher
 * - GroupStudent
 *
 * Elles permettent à partir de données brutes de récupérer un objet, et inversement (pour certaine).
 */
class AccountHydrator {

    /**
     * Récupérer un objet Account à partir de données brutes
     *
     * @param array $raw
     * @return Account
     */
    public static function unserializeAccount(array $raw): Account {
        return new Account(
            $raw['idaccount'],
            $raw['lastname'],
            $raw['firstname'],
            $raw['email'],
            AccountType::from($raw['accounttype']),
        );
    }

    /**
     * Récupérer un objet Student à partir de données brutes
     *
     * @param array $raw
     * @return Student
     */
    public static function unserializeStudent(array $raw): Student {
        return new Student(
            $raw['studentid'],
            $raw['lastname'],
            $raw['firstname'],
            $raw['email'],
            AccountType::from($raw['accounttype']),
            $raw['studentnumber'],
            self::unserializeGroupStudent($raw)
        );
    }

    /**
     * Récupérer un objet GroupStudent à partir de données brutes
     *
     * @param array $raw
     * @return GroupStudent
     */
    public static function unserializeGroupStudent(array $raw): GroupStudent {
        return new GroupStudent(
            $raw['groupid'],
            $raw['grouplabel']
        );
    }

    public static function unserializeTeacher(array $raw): Teacher {
        return new Teacher(
            $raw['idaccount'],
            $raw['lastname'],
            $raw['firstname'],
            $raw['email'],
        );
    }

    /**
     * Récupérer des données brutes à partir d'un objet Account
     *
     * @param Account $account
     * @return array
     */
    public static function serializeAccount(Account $account): array {
        return [
            "idaccount" => $account->getIdAccount(),
            "lastname" => $account->getLastName(),
            "firstname" => $account->getFirstName(),
            "email" => $account->getEmail(),
            "accounttype" => $account->getAccountType()->value,
        ];
    }

    /**
     * Récupérer des données brutes à partir d'un objet Student
     *
     * @param Student $student
     * @return array
     */
    public static function serializeStudent(Student $student): array {
        return AccountHydrator::serializeAccount($student) + [
                "studentnumber" => $student->getStudentNumber(),
                "idgroupstudent" => $student->getGroupStudent()->getIdGroupStudent(),
                "groupstudent" => $student->getGroupStudent()->getLabel()
            ];
    }
}