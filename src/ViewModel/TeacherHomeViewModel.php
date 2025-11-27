<?php
declare(strict_types=1);

namespace Uphf\GestionAbsence\ViewModel;

/**
 * Classe du ViewModel pour la page de l'enseignant
 */
readonly class TeacherHomeViewModel extends BaseViewModel
{
    public array $periods;
    public string $fullname;
    public string $currTab;

    public HeaderViewModel $headerVM;

    public function __construct(array $periods, string $currTab, string $fullname)
    {
        $this->periods = $periods;
        $this->currTab = $currTab;
        $this->fullname = $fullname;
        $this->headerVM = new HeaderViewModel(false, "Bonjour", $fullname, "!");
    }
}
