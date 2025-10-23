<?php

require_once __DIR__ . "/../connection.php";
require_once __DIR__ . "/AccountType.php";

/**
 * Classe Account, basé sur la base de données.
 */
class Account {
    protected int $idAccount;
    protected string $lastName;
    protected string $firstName;
    protected string $email;
    protected AccountType $accountType;

    public function __construct($idAccount, $lastName, $firstName, $email, $accountType) {
        $this->idAccount = $idAccount;
        $this->lastName = $lastName;
        $this->firstName = $firstName;
        $this->email = $email;
        $this->accountType = $accountType;
    }

    // Getter de base
    public function getIdAccount() : int { return $this->idAccount; }
    public function getLastName() : string { return $this->lastName; }
    public function getFirstName() : string { return $this->firstName; }
    public function getEmail() : string { return $this->email; }
    public function getAccountType() : AccountType { return $this->accountType; }

    /**
     * Serialization
     * Utilisé quand on met un objet dans $_SESSION
     * @return array
     */
    public function __serialize(): array {
        return [
            'idAccount' => $this->idAccount,
            'lastName' => $this->lastName,
            'firstName' => $this->firstName,
            'email' => $this->email,
            'accountType' => $this->accountType
        ];
    }

    /**
     * Unserialization
     * Utilisé par session_start pour récupérer un objet stocké dans la session
     * @param array $data
     * @return void
     */
    public function __unserialize(array $data): void {
        $this->idAccount = (int)$data['idAccount'];
        $this->lastName = $data['lastName'];
        $this->firstName = $data['firstName'];
        $this->email = $data['email'];
        $this->accountType = $data['accountType'];
    }

    // Utilisé pour le "login temporaire", TODO: à enlever
    public static function getAllAccount() : array {
        global $connection;

        $query = "SELECT * FROM Account";

        $req = $connection->prepare($query);
        $req->execute();

        $rows = $req->fetchAll();
        $accounts = [];

        foreach($rows as $row) {
            $accounts[$row['idaccount']] = new Account(
              $row['idaccount'],
              $row['lastname'],
              $row['firstname'],
              $row['email'],
              AccountType::from($row['accounttype'])
            );
        }

        return $accounts;
    }
}