<?php

namespace Uphf\GestionAbsence\Model\Entity\Account;

use JsonSerializable;
use Uphf\GestionAbsence\Database\Select\TableSelector;
use Uphf\GestionAbsence\Model\Hydrator\AccountHydrator;

/**
 * Classe Account, basé sur la base de données.
 */
class Account implements JsonSerializable{
    protected int $idAccount;
    protected string $lastName;
    protected string $firstName;
    protected string $email;
    protected AccountType $accountType;

    public function __construct(int $idAccount, string $lastName, string $firstName, string $email, AccountType $accountType) {
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
    public function __serialize(): array { return AccountHydrator::serializeAccount($this); }

    /**
     * Unserialization
     * Utilisé par session_start pour récupérer un objet stocké dans la session
     * @param array $data
     * @return void
     */
    public function __unserialize(array $data): void {
        $this->idAccount = (int)$data['idaccount'];
        $this->lastName = $data['lastname'];
        $this->firstName = $data['firstname'];
        $this->email = $data['email'];
        $this->accountType = AccountType::from($data['accounttype']);
    }

    public function jsonSerialize(): mixed
    {
        return [
            'idAccount' => $this->idAccount,
            'lastName' => $this->lastName,
            'firstName' => $this->firstName,
            'email' => $this->email,
            'accountType' => $this->accountType,
        ];
    }
}

