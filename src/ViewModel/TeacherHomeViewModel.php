<?php

namespace Uphf\GestionAbsence\ViewModel;

/**
 * Classe du ViewModel pour la page de l'enseignant
 */
readonly class TeacherHomeViewModel extends BaseViewModel
{
    public array $periods;
    public string $fullname;
    public array $filters;

    public HeaderViewModel $headerVM;

    public function __construct(array $periods, array $filters, string $fullname)
    {
        $this->periods = $periods;
        $this->filters = $filters;
        $this->fullname = $fullname;
        $this->headerVM = new HeaderViewModel(false, "Bonjour", $fullname, "!");
    }
}
