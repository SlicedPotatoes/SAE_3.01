<?php

namespace Uphf\GestionAbsence\ViewModel;


use Uphf\GestionAbsence\Model\Entity\Account\AccountType;

/**
 * View model pour la vue ResitSessionList
 */

readonly class ResitSessionListViewModel extends BaseViewModel {
    public array $periods;
    public array $filters;
    public HeaderViewModel $headerVM;

    public function __construct(array $periods, array $filters )
    {
        $this->periods = $periods;
        $this->filters = $filters;
        $this->headerVM = new HeaderViewModel(false,'Les', 'Rattrapages', '' );
    }
}