<?php

namespace Uphf\GestionAbsence\ViewModel;

/**
 * View model pour la vue SearchStudent
 */
readonly class SearchStudentViewModel extends BaseViewModel {
    public array $students;
    public array $listGroup;
    public array $filter;
    public HeaderViewModel $headerVM;

    public function __construct(array $students, array $listGroup, array $filter) {
        $this->students = array_map(fn($student) => new OneStudentViewModel($student), $students);
        $this->listGroup = array_map(
            fn($group) => [
                'idGroup' => $group->getIdGroupStudent(),
                'label' => $group->getLabel()
            ],
            $listGroup
        );
        $this->filter = $filter;
        $this->headerVM = new HeaderViewModel(false, "Vous pouvez rechercher ici un", "étudiant", "!");
    }
}