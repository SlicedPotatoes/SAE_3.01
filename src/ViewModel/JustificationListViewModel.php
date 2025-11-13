<?php

namespace Uphf\GestionAbsence\ViewModel;

use Uphf\GestionAbsence\Model\Entity\Account\AccountType;

/**
 * View model pour la vue JustificationList
 */
readonly class JustificationListViewModel extends BaseViewModel {
    public string $currTab;
    public AccountType $roleUser;
    public array $justificationsToDo;
    public array $justificationsDone;
    public array $filter;
    public string $fullname;

    public function __construct(
        string $currTab,
        AccountType $roleUser,
        array $justificationsToDo,
        array $justificationsDone,
        array $filter,
        string $fullname

    ) {
        $this->currTab = $currTab;
        $this->roleUser = $roleUser;
        $this->justificationsToDo = array_map(fn($j) => new OneJustificationViewModel($j, $roleUser, $j->getStudent()), $justificationsToDo);
        $this->justificationsDone = array_map(fn($j) => new OneJustificationViewModel($j, $roleUser, $j->getStudent()), $justificationsDone);
        $this->filter = $filter;
        $this->fullname = $fullname;
    }
}