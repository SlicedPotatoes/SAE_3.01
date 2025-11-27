<?php

namespace Uphf\GestionAbsence\ViewModel;


use Uphf\GestionAbsence\Model\Entity\Account\AccountType;

/**
 * View model pour la vue ResitSessionList
 */

readonly class ResitSessionListViewModel extends BaseViewModel {
    public AccountType $roleUser;
    public array $sessions;
    public array $nbAbsencePerSession;


    public function __construct(AccountType $roleUser, array $sessions, array $nbAbsencePerSession) {
        $this->roleUser = $roleUser;
        $this->nbAbsencePerSession = $nbAbsencePerSession;
    }
}