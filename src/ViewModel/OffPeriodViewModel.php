<?php

namespace Uphf\GestionAbsence\ViewModel;

/**
 * View model pour la vue listOffPeriod
 */
readonly class OffPeriodViewModel extends BaseViewModel {
    public array $periods;

    public function __construct($periods) {
        $this->periods = array_map(
            function($period) { return new OneOffPeriodViewModel($period); },
            $periods
        );
    }
}