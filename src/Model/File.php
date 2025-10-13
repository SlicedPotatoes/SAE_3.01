<?php
// Cette classe représente les fichiers stockés dans la base de données.
require_once "Justification.php";
class File {
    private int $idFile;
    private string $fileName;
    private Justification $justification;

    function __construct($idFile, $fileName, $justification) {
        $this->idFile = $idFile;
        $this->fileName = $fileName;
        $this->justification = $justification;
    }

    public function getIdFile(): int { return $this->idFile; }
    public function getFileName(): string { return $this->fileName; }
    public function getJustification(): Justification { return $this->justification; }
}