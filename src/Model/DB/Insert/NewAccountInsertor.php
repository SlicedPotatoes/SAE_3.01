<?php

namespace Uphf\GestionAbsence\Model\DB\Insert;

use Uphf\GestionAbsence\Model\DB\Connection;
use Uphf\GestionAbsence\Model\DB\Select\GroupStudentSelector;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\Model\Validation\ValidationHelper;
use Uphf\GestionAbsence\Model\Mailer;
use PDO;

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
            $sqlCheck->bindValue(":email" . $index, $student['Email'], PDO::PARAM_STR);
        }
        $sqlCheck->execute();
        $results = $sqlCheck->fetchAll(PDO::FETCH_COLUMN);
        $existingEmails = array_flip($results); // Utilisation d'array_flip pour une recherche plus rapide

        // S'assure que les groupes étudiants existent dans la base de données
        self::ensureStudentGroupsExist($students);


        // Prépare le début de la requête d'insertion afin de concaténer plusieurs valeurs à insérer et éviter plusieurs appels à la base de données
        $query = "INSERT INTO Account (lastname, firstname, email, password, accountType) VALUES ";
        $values = [];
        $params = [];
        foreach ($students as $index => $student) {
            // Si l'email de l'étudiant est déjà présent en base de données, on l'ignore pour éviter les doublons
            if (isset($existingEmails[$student['Email']])) {
                continue;
            }

            // Génération du mot de passe aléatoire et hashage
            $password = self::generatePassword();
            $hashPassword = password_hash($password, PASSWORD_DEFAULT);

            $values[] = "(:lastname" . $index . ", :firstname" . $index . ", :email" . $index . ", :password" . $index . ", :accountType" . $index . ")";
            $params[":lastname" . $index] = $student['Nom'];
            $params[":firstname" . $index] = $student['Prénom'];
            $params[":email" . $index] = $student['Email'];
            $params[":password" . $index] = $hashPassword;
            $params[":accountType" . $index] = AccountType::Student->value;

            // Envoi du mail avec le mot de passe temporaire
            Mailer::sendNewAccount(
                $student['Nom'],
                $student['Prénom'],
                $student['Email'],
                $password
            );
        }

        if (empty($values)) {
            // Aucun nouvel étudiant à insérer
            $conn->commit();
            return;
        }

        $query .= implode(", ", $values)
            . " RETURNING idaccount, email;"; // Récupère les identifiants des comptes fraîchement créés pour les utiliser dans la table Student
        $sql = $conn->prepare($query);

        // Lie les valeurs aux paramètres
        foreach ($params as $param => $value) {
            $sql->bindValue($param, $value, PDO::PARAM_STR);
        }
        $sql->execute();

        // Récupère l'identifiant du compte fraîchement créé
        $insertedAccounts = $sql->fetchAll(PDO::FETCH_ASSOC);
        $conn->commit();

        echo $insertedAccounts[0]["idaccount"] . "\n";
        self::insertStudent($insertedAccounts, $students);
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
            if (!empty($s['Email'])) {
                $studentsByEmail[strtolower($s['Email'])] = $s;
            }
        }

        // Prépare la requête d'insertion
        $query = "INSERT INTO Student (idaccount, studentnumber, idgroupstudent) VALUES ";
        $values = [];
        $params = [];

        $i = 0;
        foreach ($insertedAccounts as $account) {
            $email = $account['email'] ?? $account['Email'] ?? null;
            if ($email === null)
            {
                continue; // pas d'information étudiante correspondante
            }

            $email = strtolower($email);
            if (!isset($studentsByEmail[$email]))
            {
                /**
                 * Si il n'y a pas de compte qui ont ce mail
                 */
                continue;
            }

            $student = $studentsByEmail[$email];

            $values[] = "(:idAccount{$i}, :studentNumber{$i}, :idGroupStudent{$i})";
            $params[":idAccount{$i}"] = $account['idaccount'] ?? $account['idAccount'] ?? null;
            $params[":studentNumber{$i}"] = $student['Identifiant'];
            $params[":idGroupStudent{$i}"] = GroupStudentSelector::getGroupStudentByLabel($student['Diplômes'])->getIdGroupStudent();
            $i++;
        }
        if (empty($values)) {
            $conn->commit();
            return;
        }

        $query .= implode(", ", $values) . ";";
        $sql = $conn->prepare($query);

        foreach ($params as $param => $value) {
            $sql->bindValue($param, $value, PDO::PARAM_STR);
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
        $queryCheck = "SELECT groupID, groupLabel FROM GroupStudent";
        $sqlCheck = $conn->prepare($queryCheck);
        $sqlCheck->execute();
        $results = $sqlCheck->fetchAll(PDO::FETCH_ASSOC);
        foreach ($results as $row) {
            $existingGroups[$row['grouplabel']] = $row['groupid'];
        }

        // On vérifie chaque étudiant pour voir si son groupe existe, sinon on l'ajoute
        $queryInsert = "INSERT INTO GroupStudent (groupLabel) VALUES ";
        $values = [];
        $params = [];

        foreach ($students as $student) {
            $groupLabel = $student['Diplômes'];
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
                $sqlInsert->bindValue($param, $value, PDO::PARAM_STR);
            }
            $sqlInsert->execute();
        }
    }


    /**
     * Insère un nouveau compte de type Educational Manager dans la table `Account` avec les champs suivants :
     * - `lastname` : nom de famille (string)
     * - `firstname` : prénom (string)
     * - `email` : adresse email (string)
     * - `password` : mot de passe hashé (string) (généré aléatoirement)
     * - `accountType` : type de compte (int) (AccountType::EducationalManager)
     * <br/><br/>
     *
     * Ensuite, elle appelle la méthode insertTeacher pour insérer les informations spécifiques au gestionnaire éducatif dans la table `Teacher` avec le champ suivant :
     * - `idAccount` : identifiant du compte (int) récupéré après l'insertion dans la table `Account`
     * <br/><br/>
     *
     * Ensuite, elle envoie un email à l'adresse fournie avec le mot de passe temporaire.
     *
     * @param string $lastname nom de famille
     * @param string $firstname prénom
     * @param string $email adresse email
     * @return void
     */
    public static function insertEducationalManagerAccount(string $lastname, string $firstname, string $email): void
    {
        // Récupère la connexion
        $conn = Connection::getInstance();

        // Génération du mot de passe aléatoire et hashage
        $password = self::generatePassword();
        $hashPassword = password_hash($password, PASSWORD_DEFAULT);

        // Prépare la requête d'insertion
        $query = "INSERT INTO Account (lastname, firstname, email, password, accountType) 
                  VALUES (:lastname, :firstname, :email, :password, :accountType)
                  RETURNING idAccount;";

        $sql = $conn->prepare($query);

        // Lie les valeurs aux paramètres
        $sql->bindValue(":lastname", $lastname, PDO::PARAM_STR);
        $sql->bindValue(":firstname", $firstname, PDO::PARAM_STR);
        $sql->bindValue(":email", $email, PDO::PARAM_STR);
        $sql->bindValue(":password", $hashPassword, PDO::PARAM_STR);
        $sql->bindValue(":accountType", AccountType::EducationalManager, PDO::PARAM_INT);

        $sql->execute();

        $insertedAccounts = $sql->fetchAll(PDO::FETCH_ASSOC);
        self::insertTeacher($insertedAccounts);

        // Envoi du mail avec le mot de passe temporaire
        Mailer::sendNewAccountEmail(
            $lastname,
            $firstname,
            $email,
            $password
        );
    }


    /**
     * Insère un ou plusieurs nouveaux comptes de type Enseignant dans la table `Account` avec les champs suivants :
     * - `lastname` : nom de famille (string)
     * - `firstname` : prénom (string)
     * - `email` : adresse email (string)
     * - `password` : mot de passe hashé (string) (généré aléatoirement)
     * - `accountType` : type de compte (int) (AccountType::Teacher)
     * <br/><br/>
     *
     * Ensuite, elle appelle la méthode insertTeacher pour insérer les informations spécifiques à l'enseignant dans la table `Teacher` avec le champ suivant :
     * - `idAccount` : identifiant du compte (int) récupéré après l'insertion dans la table `Account`
     * <br/><br/>
     *
     * Ensuite, elle envoie un email à chaque adresse fournie avec le mot de passe temporaire.
     *
     * @param array $teachers tableau d'éléments ['lastname' => ..., 'firstname' => ..., 'email' => ...]
     * @return void
     */
    public static function insertTeacherAccount(array $teachers): void
    {
        // Récupère la connexion
        $conn = Connection::getInstance();
        $conn->beginTransaction();

        // Prépare le début de la requête d'insertion afin de concaténer plusieurs valeurs à insérer et éviter plusieurs appels à la base de données
        $query = "INSERT INTO Account (lastname, firstname, email, password, accountType) VALUES ";
        $values = [];
        $params = [];

        foreach ($teachers as $index => $teacher) {
            // Génération du mot de passe aléatoire et hashage
            $password = self::generatePassword();
            $hashPassword = password_hash($password, PASSWORD_DEFAULT);

            $values[] = "(:lastname" . $index . ", :firstname" . $index . ", :email" . $index . ", :password" . $index . ", :accountType" . $index . ")";
            $params[":lastname" . $index] = $teacher['lastname'];
            $params[":firstname" . $index] = $teacher['firstname'];
            $params[":email" . $index] = $teacher['email'];
            $params[":password" . $index] = $hashPassword;
            $params[":accountType" . $index] = AccountType::Teacher;

            // Envoi du mail avec le mot de passe temporaire
            Mailer::sendNewAccountEmail(
                $teacher['lastname'],
                $teacher['firstname'],
                $teacher['email'],
                $password
            );
        }
        $query .= implode(", ", $values);

        // On récupère l'identifiant du compte fraîchement créé
        $query .= " RETURNING idAccount;";

        $sql = $conn->prepare($query);
        // Lie les valeurs aux paramètres
        foreach ($params as $param => $value) {
            $sql->bindValue($param, $value, PDO::PARAM_STR);
        }

        $sql->execute();
        $conn->commit();

        $insertedAccounts = $sql->fetchAll(PDO::FETCH_ASSOC);
        self::insertTeacher($insertedAccounts);

    }


    /**
     * Cette méthode sera lancée par une autre méthode pour faire le lien entre le compte fraîchement créé son rôle d'enseignant.
     * Insère un ou plusieurs `idAccount` dans la table `Teacher`.
     * - `idAccount` : identifiant du compte (int)
     * @param array $insertedAccounts tableau d'éléments ['idAccount' => ...] des comptes fraîchement créés
     * @return void
     */
    public static function insertTeacher(array $insertedAccounts): void
    {
        $conn = Connection::getInstance();
        $conn->beginTransaction();

        // Prépare la requête d'insertion
        $query = "INSERT INTO Teacher (idAccount) VALUES ";
        $values = [];
        $params = [];

        $i = 0;
        foreach ($insertedAccounts as $account) {
            $values[] = "(:idAccount{$i})";
            $params[":idAccount{$i}"] = $account['idAccount'];
            $i++;
        }

        if (empty($values)) {
            $conn->commit();
            return;
        }

        $query .= implode(", ", $values) . ";";
        $sql = $conn->prepare($query);

        foreach ($params as $param => $value) {
            $sql->bindValue($param, $value, PDO::PARAM_STR);
        }

        $sql->execute();
        $conn->commit();
    }


    /**
     * Insère un nouveau compte de type Secrétaire dans la table `Account` avec les champs suivants :
     * - `lastname` : nom de famille (string)
     * - `firstname` : prénom (string)
     * - `email` : adresse email (string)
     * - `password` : mot de passe hashé (string) (généré aléatoirement)
     * - `accountType` : type de compte (int) (AccountType::Secretary)
     * <br/><br/>
     * Ensuite, elle envoie un email à l'adresse fournie avec le mot de passe temporaire.
     *
     * @param string $lastname
     * @param string $firstname
     * @param string $email
     * @return void
     */
    public static function insertSecretaryAccount(string $lastname, string $firstname, string $email): void
    {
        // Récupère la connexion
        $conn = Connection::getInstance();

        // Génération du mot de passe aléatoire et hashage
        $password = self::generatePassword();
        $hashPassword = password_hash($password, PASSWORD_DEFAULT);

        // Prépare la requête d'insertion
        $query = "INSERT INTO Account (lastname, firstname, email, password, accountType) 
                  VALUES (:lastname, :firstname, :email, :password, :accountType);";

        $sql = $conn->prepare($query);

        // Lie les valeurs aux paramètres
        $sql->bindValue(":lastname", $lastname, PDO::PARAM_STR);
        $sql->bindValue(":firstname", $firstname, PDO::PARAM_STR);
        $sql->bindValue(":email", $email, PDO::PARAM_STR);
        $sql->bindValue(":password", $hashPassword, PDO::PARAM_STR);
        $sql->bindValue(":accountType", AccountType::Secretary, PDO::PARAM_INT);

        $sql->execute();

        // Envoi du mail avec le mot de passe temporaire
        Mailer::sendNewAccountEmail(
            $lastname,
            $firstname,
            $email,
            $password
        );
    }


    /**
     * Génère un mot de passe aléatoire de 16 caractères respectant les critères suivants :
     * - Contient au moins une majuscule
     * - Contient au moins une minuscule
     * - Contient au moins un chiffre
     * - Contient au moins un caractère spécial
     *
     * @return string Le mot de passe généré
     */
    public static function generatePassword()
    {
        $password = "";
        $length = 16;

        $upper = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $lower = "abcdefghijklmnopqrstuvwxyz";
        $digits = "0123456789";
        $special = "!@#$%^&*()-_=+[]{}|;:,.<>?";
        $allChars = $upper . $lower . $digits . $special;

        // Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial
        $password .= $upper[random_int(0, strlen($upper) - 1)];
        $password .= $lower[random_int(0, strlen($lower) - 1)];
        $password .= $digits[random_int(0, strlen($digits) - 1)];
        $password .= $special[random_int(0, strlen($special) - 1)];

        // on complète le mot de passe avec des caractères aléatoires
        for ($i = 4; $i < $length; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }

        // on mélange les caractères du mot de passe pour éviter un ordre prévisible
        $password = str_shuffle($password);

        //on vérifie que le mot de passe respecte les critères
        if (ValidationHelper::validatePassword($password) == $password) {
            return $password;
        } else {
            // si le mot de passe ne respecte pas les critères, on génère un nouveau mot de passe
            return self::generatePassword();
        }
    }
}