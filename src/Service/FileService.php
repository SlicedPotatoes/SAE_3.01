<?php

namespace Uphf\GestionAbsence\Service;

use PDO;
use Uphf\GestionAbsence\Database\Select\JustificationSelector;
use Uphf\GestionAbsence\Database\Select\TableSelector;
use Uphf\GestionAbsence\Exception\EntityNotFoundException;
use Uphf\GestionAbsence\Model\Entity\Account\Account;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\Model\Entity\Justification\File;
use Uphf\GestionAbsence\Model\Hydrator\JustificationHydrator;

class FileService {

    /**
     * Retourne true si l'utilisateur peut consulter le fichier, sinon false
     *
     * @param File $file
     * @param Account $account
     * @return bool
     */
    public static function userCanSeeFile(File $file, Account $account) : bool {
        if($account->getAccountType() === AccountType::EducationalManager) {
            return true;
        }

        $justification = JustificationSelector::getJustificationById($file->getJustification());
        return $justification->getStudent()->getIdAccount() === $account->getIdAccount();
    }

    /**
     * Renvoie un objet de type File à partir d'un identifiant
     *
     * @param $id
     * @return File
     * @throws EntityNotFoundException Dans le cas ou le fichier n'existe pas
     */
    public static function getFile($id): File {
        $file = TableSelector::fromTableWhere("file", ['idfile'], [[$id, PDO::PARAM_INT]]);

        if(count($file) === 0) {
            throw new EntityNotFoundException("Le fichier n'existe pas");
        }

        return JustificationHydrator::unserializeFile($file[0]);
    }
}