<?php

namespace Uphf\GestionAbsence\ViewModel;

use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\Model\Entity\Justification\Justification;

/**
 * View model pour la vue DetailJustification
 */
readonly class DetailJustificationViewModel extends BaseViewModel {
    public OneJustificationViewModel $justification;
    public array $absences;
    public array $files;
    public AccountType $roleUser;
    public OneStudentViewModel $student;
    public array $comments;

    public function __construct(
        Justification $justification,
        array $absences,
        array $files,
        AccountType $roleUser,
        array $comments = []
    ) {
        $this->justification = new OneJustificationViewModel($justification, $roleUser);
        $this->absences = array_map(fn($abs) => new OneAbsenceViewModel($abs), $absences);
        $this->files = array_map(fn($file) => [
            'idFile' => $file->getIdFile(),
            'fileName' => $file->getFileName(),
            'originalName' => ""
        ], $files);
        $this->roleUser = $roleUser;
        $this->student = new OneStudentViewModel($justification->getStudent());
        $this->comments = array_map(fn($comment) => [
            'idComment' => $comment->getIdComment(),
            'label' => $comment->getTextComment()
        ], $comments);
    }
}