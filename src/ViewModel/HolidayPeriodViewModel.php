<?php

namespace Uphf\GestionAbsence\ViewModel;

readonly class HolidayPeriodViewModel extends BaseViewModel {
    public array $periods;

    public function __construct($periods) {
        $this->periods = array_map(function($period) {
            return new OneHolidayPeriodViewModel($period);
        }, $periods);
    }
}