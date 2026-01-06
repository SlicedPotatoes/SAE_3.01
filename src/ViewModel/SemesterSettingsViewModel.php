<?php

namespace Uphf\GestionAbsence\ViewModel;

use Uphf\GestionAbsence\Model\Entity\Semester;

/**
 * View model pour la vue semesterSettings
 */
readonly class SemesterSettingsViewModel extends BaseViewModel {
    public OneSemesterViewModel $semester1;
    public OneSemesterViewModel $semester2;
    public HeaderViewModel $headerVM;

    /**
     * @param Semester[] $semesters
     */
    public function __construct(array $semesters) {
        $this->semester1 = new OneSemesterViewModel($semesters[0]);
        $this->semester2 = new OneSemesterViewModel($semesters[1]);
        $this->headerVM = new HeaderViewModel(false, 'Définissez les', 'semestres', 'de l\'année actuelle');
    }
}
