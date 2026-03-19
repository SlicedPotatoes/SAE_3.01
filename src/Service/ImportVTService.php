<?php

namespace Uphf\GestionAbsence\Service;

use Uphf\GestionAbsence\Database\Insert\AbsenceInsertor;
use Uphf\GestionAbsence\Database\Insert\NewAccountInsertor;
use Uphf\GestionAbsence\Model\ReaderCSV;
use Uphf\GestionAbsence\Model\Validation\ImportAbsenceValidator;

class ImportVTService
{
    private static array $absenceColumns = array(
        'Nom',
        'Prénom',
        'Prénom 2',
        'Date de naissance',
        'Identifiant',
        'Diplômes',
        'Composante',
        'Public',
        'Date',
        'Heure',
        'Durée',
        'Type',
        'Matière',
        'Identifiant matière',
        'Enseignement',
        "Identifiant de l'enseignement",
        'Absent/Présent',
        'Justification',
        'Motif absence',
        'Commentaire',
        'Groupes',
        'Salles',
        'Profs',
        'Contrôle');

    private static array $studentColumns = array(
        'Nom',
        'Prénom',
        'Prénom 2',
        'Date de naissance',
        'Identifiant',
        'Diplômes',
        'Public',
        'Email',
        'Identifiant national');

    public static function import(array $file)
    {
        if (!ReaderCSV::isCSV($file['name'])) {
            throw new \Exception("Aucun fichier CSV valide fourni.");
        }

        $data = ReaderCSV::readCSV($_FILES['vt_file']['tmp_name']);

        if (ReaderCSV::haveCollum($data, ImportVTService::$absenceColumns)) {
            ImportVTService::importAbsences($data);
            return true;
        }

        if (ReaderCSV::haveCollum($data, ImportVTService::$studentColumns)) {
            ImportVTService::importStudents($data);
            return true;
        }

        throw new \Exception("Le fichier CSV ne correspond pas aux critères.");

    }

    private static function importStudents(array $data)
    {
        NewAccountInsertor::insertStudentAccount($data);
    }

    private static function importAbsences(array $data)
    {
        $validator = new ImportAbsenceValidator($data);
        $data = $validator->getData();
        /**
         * Si apres validation du format de données, le fichier est vide
         */
        if(count($data) === 0) {
            throw new \Exception("Aucune données valide dans le fichier.");
        }

        [$nbAbs, $nbAbsWithoutDuplication] = AbsenceInsertor::addAbsences($data);

        if($nbAbs != $nbAbsWithoutDuplication) {
            throw new \Exception("$nbAbsWithoutDuplication sur $nbAbs Absences importé avec succès. (Cause doublon)");
        }
    }

}