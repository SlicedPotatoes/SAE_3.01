<?php

namespace Uphf\GestionAbsence\ViewModel;

/**
 * View model pour la vue semesterSettings
 */
readonly class SemesterSettingsViewModel extends BaseViewModel {
    public object $semester1;
    public object $semester2;
    public HeaderViewModel $headerVM;

    public function __construct(?object $semester1 = null, ?object $semester2 = null) {
        // Valeurs par défaut si pas de données en BDD
        $this->semester1 = $semester1 ?? (object)[
            'id' => 1,
            'label' => 'Semestre 1',
            'startDate' => '2024-09-01',
            'endDate' => '2025-01-15'
        ];

        $this->semester2 = $semester2 ?? (object)[
            'id' => 2,
            'label' => 'Semestre 2',
            'startDate' => '2025-01-16',
            'endDate' => '2025-06-30'
        ];

        $this->headerVM = new HeaderViewModel(false, 'Définissez les', 'semestres', 'de l\'année actuelle');
    }
}
