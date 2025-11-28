<?php

namespace Uphf\GestionAbsence\ViewModel;

/**
 * View model pour la vue listOffPeriod
 */
readonly class OffPeriodViewModel extends BaseViewModel {
    public array $periods;
    public HeaderViewModel $headerVM;

    public function __construct($periods) {
        $this->periods = array_map(
            function($period) { return new OneOffPeriodViewModel($period); },
            $periods
        );
        $this->headerVM = new HeaderViewModel(false, 'Planifiez vos', 'périodes de congés', 'en toute simplicité');
    }
}