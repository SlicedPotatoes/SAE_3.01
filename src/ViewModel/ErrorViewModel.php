<?php

namespace Uphf\GestionAbsence\ViewModel;

/**
 * View model pour la vue Error
 */
readonly class ErrorViewModel extends BaseViewModel {
    public int $errorCode;
    public string $errorMessage1;
    public string $errorMessage2;

    public function __construct(int $errorCode, string $errorMessage1, string $errorMessage2) {
        $this->errorCode = $errorCode;
        $this->errorMessage1 = $errorMessage1;
        $this->errorMessage2 = $errorMessage2;
    }
}