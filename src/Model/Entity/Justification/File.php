<?php
namespace Uphf\GestionAbsence\Model\Entity\Justification;

/**
 *  Classe File, basé sur la base de données.
 */
class File {
    private int $idFile;
    private string $fileName;
    private int | Justification $justification;

    function __construct($idFile, $fileName, $justification) {
        $this->idFile = $idFile;
        $this->fileName = $fileName;
        $this->justification = $justification;
    }

    // Getter de base
    public function getIdFile(): int { return $this->idFile; }
    public function getFileName(): string { return $this->fileName; }
    public function getJustification(): int | Justification { return $this->justification; }
}