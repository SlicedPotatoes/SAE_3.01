<?php

namespace Uphf\GestionAbsence\Model\DB\Insert;

use Uphf\GestionAbsence\Model\DB\Connection;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\Model\Validation\ValidationHelper;

/**
 * Cette classe permet l'insertion de nouveau compte dans la BDD.
 */
class NewAccountInsertor
{
    // à modifier pour prendre en paramètre un tableau d'étudiant
    /**
     * Cette méthode insère un nouveau compte dans la table `Account` avec les champs suivants :
     * - `lastname` : nom de famille (string)
     * - `firstname` : prénom (string)
     * - `email` : adresse email (string)
     * - `password` : mot de passe hashé (string) (par défaut : mot de passe généré aléatoirement)
     * - `accountType` : type de compte (int) (par défaut : AccountType::Student)
     * <br/><br/>
     * Ensuite, elle appelle la méthode insertStudent pour insérer les informations spécifiques à l'étudiant dans la table `Student` avec les champs suivants :
     * - `idAccount` : identifiant du compte (int) récupéré après l'insertion dans la table `Account`
     * - `studentNumber` : numéro étudiant (string|int)
     * - `idGroupStudent` : identifiant du groupe étudiant (int|null)
     *
     * @param array $students tableau d'éléments ['lastname' => ..., 'firstname' => ..., 'email' => ..., 'studentNumber' => ..., 'idGroupStudent' => ...]
     * @return void
     */
    public static function insertStudentAccount(array $students): void
    {
        // Récupère la connexion et définie une transaction
        $conn = Connection::getInstance();
        $conn->beginTransaction();

        // Récupérations des étudiants déjà présents en base de données pour éviter les doublons
        $existingEmails = [];
        $queryCheck = "SELECT email FROM Account WHERE email IN (";
        $placeholders = [];
        foreach ($students as $index => $student) {
            $placeholders[] = ":email" . $index;
        }
        $queryCheck .= implode(", ", $placeholders) . ");";
        $sqlCheck = $conn->prepare($queryCheck);
        foreach ($students as $index => $student) {
            $sqlCheck->bindValue(":email" . $index, $student['email'], \PDO::PARAM_STR);
        }
        $sqlCheck->execute();
        $results = $sqlCheck->fetchAll(\PDO::FETCH_COLUMN);
        $existingEmails = array_flip($results); // Utilisation d'array_flip pour une recherche plus rapide

        // S'assure que les groupes étudiants existent dans la base de données
        self::ensureStudentGroupsExist($students);


        // Prépare le début de la requête d'insertion afin de concaténer plusieurs valeurs à insérer et éviter plusieurs appels à la base de données
        $query = "INSERT INTO Account (lastname, firstname, email, password, accountType) VALUES ";
        $values = [];
        $params = [];
        foreach ($students as $index => $student) {
            // Si l'email de l'étudiant est déjà présent en base de données, on l'ignore pour éviter les doublons
            if (isset($existingEmails[$student['email']])) {
                continue;
            }

            // Génération du mot de passe aléatoire et hashage


            $password = ValidationHelper::generateRandomPassword();
            $hashPassword = password_hash($password, PASSWORD_DEFAULT);

            $values[] = "(:lastname" . $index . ", :firstname" . $index . ", :email" . $index . ", :password" . $index . ", :accountType" . $index . ")";
            $params[":lastname" . $index] = $student['lastname'];
            $params[":firstname" . $index] = $student['firstname'];
            $params[":email" . $index] = $student['email'];
            $params[":password" . $index] = $hashPassword;
            $params[":accountType" . $index] = AccountType::Student;

            // Envoi du mail avec le mot de passe temporaire
            Mailer::sendNewAccountEmail(
                $student['lastname'],
                $student['firstname'],
                $student['email'],
                $password
            );
        }
        if (empty($values)) {
            // Aucun nouvel étudiant à insérer
            $conn->commit();
            return;
        }
        $query .= implode(", ", $values)
            . " RETURNING idAccount, email;"; // Récupère les identifiants des comptes fraîchement créés pour les utiliser dans la table Student
        $sql = $conn->prepare($query);

        // Lie les valeurs aux paramètres
        foreach ($params as $param => $value) {
            $sql->bindValue($param, $value, \PDO::PARAM_STR);
        }
        $sql->execute();

        // Récupère l'identifiant du compte fraîchement créé
        $insertedAccounts = $sql->fetchAll(\PDO::FETCH_ASSOC);
        $conn->commit();
    }


    /**
     * Cette méthode sera lancée par une autre méthode pour faire le lien entre le compte fraîchement créé et l'étudiant.
     * Insère un ou plusieurs enregistrements dans la table `Student` avec les champs suivants :
     *  - `idAccount` : identifiant du compte (int)
     *  - `studentNumber` : numéro étudiant (string|int)
     *  - `idGroupStudent` : identifiant du groupe étudiant (int|null)
     *
     * @param array $insertedAccounts tableau d'éléments ['idAccount' => ..., 'email' => ...] des comptes fraîchement créés
     * @param array $originalStudents tableau d'éléments ['lastname' => ..., 'firstname' => ..., 'email' => ..., 'studentNumber' => ..., 'idGroupStudent' => ...] des étudiants originaux
     * @return void
     */
    public static function insertStudent(array $insertedAccounts, array $originalStudents): void
    {
        $conn = Connection::getInstance();
        $conn->beginTransaction();

        // Construire un index des étudiants originaux par email pour lookup O(1)
        $studentsByEmail = [];
        foreach ($originalStudents as $s) {
            if (isset($s['email'])) {
                $studentsByEmail[$s['email']] = $s;
            }
        }

        // Prépare la requête d'insertion
        $query = "INSERT INTO Student (idAccount, studentNumber, idGroupStudent) VALUES ";
        $values = [];
        $params = [];

        $i = 0;
        foreach ($insertedAccounts as $account) {
            $email = $account['email'] ?? null;
            if ($email === null || !isset($studentsByEmail[$email])) {
                continue; // pas d'information étudiante correspondante
            }
            $student = $studentsByEmail[$email];

            $values[] = "(:idAccount{$i}, :studentNumber{$i}, :idGroupStudent{$i})";
            $params[":idAccount{$i}"] = $account['idAccount'];
            $params[":studentNumber{$i}"] = $student['studentNumber'] ?? null;
            $params[":idGroupStudent{$i}"] = $student['idGroupStudent'] ?? null;
            $i++;
        }

        if (empty($values)) {
            $conn->commit();
            return;
        }

        $query .= implode(", ", $values) . ";";
        $sql = $conn->prepare($query);

        foreach ($params as $param => $value) {
            $sql->bindValue($param, $value, \PDO::PARAM_STR);
        }

        $sql->execute();
        $conn->commit();
    }


    /**
     * Cette fonction vérifiera si le studentGroup existe dans la base de données.
     * Si ce n'est pas le cas, elle l'ajoutera.
     * @param array $students tableau d'éléments
     * @return void
     */
    public static function ensureStudentGroupsExist(array $students): void
    {
        // Récupère la connexion
        $conn = Connection::getInstance();

        // Récupérer les groupes étudiants existants
        $existingGroups = [];
        $queryCheck = "SELECT groupID, groupLabel FROM GroupStudent;";
        $sqlCheck = $conn->prepare($queryCheck);
        $sqlCheck->execute();
        $results = $sqlCheck->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($results as $row) {
            $existingGroups[$row['groupLabel']] = $row['groupID'];
        }

        // On vérifie chaque étudiant pour voir si son groupe existe, sinon on l'ajoute
        $queryInsert = "INSERT INTO GroupStudent (groupLabel) VALUES ";
        $values = [];
        $params = [];

        foreach ($students as $student) {
            $groupLabel = $student['groupLabel'];
            if (!isset($existingGroups[$groupLabel])) {
                // Insérer le nouveau groupe étudiant
                $values[] = "(:groupLabel" . count($params) . ")";
                $params[":groupLabel" . count($params)] = $groupLabel;
            }
        }
        if (!empty($values)) {
            $queryInsert .= implode(", ", $values) . ";";
            $sqlInsert = $conn->prepare($queryInsert);
            // Lie les valeurs aux paramètres
            foreach ($params as $param => $value) {
                $sqlInsert->bindValue($param, $value, \PDO::PARAM_STR);
            }
            $sqlInsert->execute();
        }
    }
}