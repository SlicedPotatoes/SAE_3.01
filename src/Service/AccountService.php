<?php

namespace Uphf\GestionAbsence\Service;

use Uphf\GestionAbsence\Database\Delete\TokenDelete;
use Uphf\GestionAbsence\Database\Select\AccountSelector;
use Uphf\GestionAbsence\Database\Select\SelectBuilder\StudentSelectBuilder;
use Uphf\GestionAbsence\Database\Select\StudentSelector;
use Uphf\GestionAbsence\Database\Update\PasswordUpdate;
use Uphf\GestionAbsence\Exception\BadCredentialException;
use Uphf\GestionAbsence\Exception\EntityNotFoundException;
use Uphf\GestionAbsence\Model\Entity\Account\Account;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\Model\Entity\Account\Student;

/**
 * Service responsable de la gestion des comptes utilisateurs
 *
 * Il regroupe la logique métier liée aux comptes :
 * - Authentification d'un utilisateur
 * - Changement de mot de passe
 * - Gestion des réinitialisations par token
 * - Récupération d'étudiants selon différents filtres
 */
class AccountService {
    /**
     * Permet de récupérer un object Account pour un email et mot de passe donnés
     *
     * Dans le cas d'un compte de type étudiant, un object Student est retourné
     *
     * @param $email string
     * @param $password string
     * @return Account
     * @throws BadCredentialException En cas de mot de passe incorrect
     * @throws EntityNotFoundException Si le compte n'existe pas
     */
    public static function loginAccount(string $email, string $password): Account {
        $account = AccountSelector::getAccountByEmail($email);

        if($account === null) {
            throw new EntityNotFoundException("Account not found");
        }
        if(!password_verify($password, AccountSelector::getPasswordHashedById($account->getIdAccount()))) {
            throw new BadCredentialException("Incorrect password");
        }

        if($account->getAccountType() === AccountType::Student) {
            $account = StudentSelector::getStudentById($account->getIdAccount());
        }

        return $account;
    }

    /**
     * Changer le mot de passe d'un compte avec comme authentification son ancien mot de passe
     *
     * @param $accountId int
     * @param $oldPassword string
     * @param $newPassword string
     * @return void
     * @throws EntityNotFoundException Si le compte n'existe pas
     * @throws BadCredentialException Mot de passe incorrecte
     */
    public static function changePasswordWithOldPassword(int $accountId, string $oldPassword, string $newPassword): void {
        $currHash = AccountSelector::getPasswordHashedById($accountId);

        if($currHash === null) {
            throw new EntityNotFoundException("Account with id " . $accountId . " not found");
        }
        if(!password_verify($oldPassword, $currHash)) {
            throw new BadCredentialException("Incorrect password");
        }

        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        PasswordUpdate::updatePassword($accountId, $passwordHash);
    }

    /**
     * Changer le mot de passe d'un compte à partir d'un token d'authentification
     *
     * @param $token string
     * @param $newPassword string
     * @return void
     * @throws EntityNotFoundException Dans le cas ou aucun token actif (non expiré) n'a été trouvé
     */
    public static function changePasswordWithToken(string $token, string $newPassword): void {
        $account = AccountSelector::getAccountFromToken($token);

        if($account === null) {
            throw new EntityNotFoundException("Invalid or expired token");
        }

        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        PasswordUpdate::updatePassword($account->getIdAccount(), $passwordHash);
        TokenDelete::deleteToken($token);
    }

    /**
     * À partir d'une liste de filtres, récupère les étudiants validant les filtres.
     *
     * @param $filters array Dictionnaire avec deux clés :
     *   - "search" -> String représentant la recherche
     *   - "groupStudent" -> int représentant l'id d'un groupStudent
     * @return Student[]
     */
    public static function getFilteredStudents(array $filters): array {
        $builderStudents = new StudentSelectBuilder();

        if($filters['search'] !== null) {
            $builderStudents->searchBar($filters['search']);
        }
        else {
            $builderStudents->allStudent();
        }

        if($filters['groupStudent'] !== null) {
            $builderStudents->groupStudent($filters['groupStudent']);
        }

        return $builderStudents->execute();
    }

    /**
     * Récupérer un étudiant à partir de son ID
     *
     * @param int $id
     * @return Student
     * @throws EntityNotFoundException Dans le cas ou l'étudiant n'existe pas
     */
    public static function getStudentAccount(int $id): Student {
        $account = StudentSelector::getStudentById($id);

        if($account === null) {
            throw new EntityNotFoundException("Student with id " . $id . " not found");
        }

        return $account;
    }
}