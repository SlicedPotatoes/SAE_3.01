<?php

namespace Uphf\GestionAbsence\Validator;

use Exception;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;
use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\Entity\Absence\StateAbs;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;

/**
 * Classe responsable de la validation des entrées utilisateurs du controller StudentProfileControllerApi
 */
class StudentProfileValidator {

    /**
     * Renvoie une exception, si les données ne sont pas au format suivant :
     *
     *  - "dateStart" (optionnel), de type string, représentant une date au format "YYYY-mm-dd"
     *  - "dateEnd" (optionnel), de type string, représentant une date au format "YYYY-mm-dd"
     *  - "state" (optionnel), de type string, qui a soit la valeur "Processed" ou "NotProcessed"
     *  - "examen" (optionnel), de type bool
     *  - "lock" (optionnel), de type bool
     *  - "idStudent" (optionnel), de type int
     *
     * @param array $data
     * @return void
     * @throws NestedValidationException
     * @throws Exception Dans le cas d'une demande du RP, sans précision de l'idStudent
     */
    public static function validationGetAbsences(array &$data): void {
        $validator = v::key('startDate', v::date('Y-m-d'), false)
            ->key('endDate', v::date('Y-m-d'), false)
            ->key('state', v::in(['Validated', 'Refused', 'NotJustified', 'Pending']), false)
            ->key('examen', v::boolType(), false)
            ->key('lock', v::boolType(), false)
            ->key('idStudent', v::intVal(), false);

        $validator->assert($data);

        // Forcé dans le cas d'une requête de l'étudiant, que son id sois utilisé dans le filtre idStudent
        if(AuthManager::isRole(AccountType::Student)) {
            $data['idStudent'] = AuthManager::getAccount()->getIdAccount();
        }
        else if(AuthManager::isRole(AccountType::EducationalManager) && !isset($data['idStudent'])) {
            throw new Exception("Aucun étudiant n'a été fournis");
        }

        // Convertir le string en enumération
        if(isset($data['state'])) {
            $data['state'] = StateAbs::from($data['state']);
        }
    }

    /**
     * Renvoie une exception, si les données ne sont pas au format suivant :
     *
     *  - Une clé "filters" (optionnel), de type map, pouvant contenir les clés suivantes :
     *      - "dateStart", de type string, représentant une date au format "YYYY-mm-dd"
     *      - "dateEnd", de type string, représentant une date au format "YYYY-mm-dd"
     *      - "state", de type string, qui a soit la valeur "Processed" ou "NotProcessed"
     *      - "examen", de type bool
     *      - "idStudent", de type int
     *  - Une clé "orderOptions" (optionnel) de type map, non vide, avec obligatoirement les clés suivantes :
     *      - "columns", de type array, ou chaque valeur est un string non vide
     *      - "sortOrder", de type string, qui a soit la valeur "ASC" ou "DESC"
     *
     * @param array $data
     * @return void
     * @throws NestedValidationException
     * @throws Exception Dans le cas ou la date de début est supérieur a la date de fin
     */
    public static function validationPostJustification(array $data): void {
        $validator = v::key('startDate', v::date('Y-m-d'))
            ->key('endDate', v::date('Y-m-d'))
            ->key('absenceReason', v::stringType()->notEmpty());

        $validator->assert($data);

        if(\DateTime::createFromFormat("Y-m-d", $data['startDate']) > \DateTime::createFromFormat("Y-m-d", $data['endDate'])) {
            throw new Exception("La date de début dois être inférieure ou égal a la date de fin");
        }
    }
}