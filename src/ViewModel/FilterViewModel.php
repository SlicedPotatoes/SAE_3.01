<?php

namespace Uphf\GestionAbsence\ViewModel;

readonly class FilterViewModel extends BaseViewModel {
    public array $filter;
    public bool $showState;

    public function __construct(array $filter, bool $showState) {
        $this->filter = $filter;
        $this->showState = $showState;
    }
}