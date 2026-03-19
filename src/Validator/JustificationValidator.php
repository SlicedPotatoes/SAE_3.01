<?php

namespace Uphf\GestionAbsence\Validator;

use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;
use Uphf\GestionAbsence\Database\Select\SelectBuilder\SortOrder;
use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\Entity\Absence\StateAbs;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\Model\Entity\Justification\StateJustif;
use Uphf\GestionAbsence\Validator\Rules\CompactAbsence;

/**
 * Classe responsable de la validation des entrées utilisateurs du controller JustificationControllerApi
 */
class JustificationValidator {

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
     */
    public static function validationGetJustificationList(array &$data): void {
        $validator = v::key(
            'filters',
            v::key('dateStart', v::date('Y-m-d'), false)
            ->key('dateEnd', v::date('Y-m-d'), false)
            ->key('state', v::in(['Processed', 'NotProcessed']), false)
            ->key('examen', v::boolType(), false)
            ->key('idStudent', v::intType(), false),
            false
        )->key(
            'orderOptions',
            v::key('columns', v::arrayType()->each(v::stringType()->notEmpty()))
            ->key('sortOrder', v::in(['ASC', 'DESC'])),
            false
        );

        $validator->assert($data);

        // Forcé dans le cas d'une requête de l'étudiant, que son id sois utilisé dans le filtre idStudent
        if(AuthManager::isRole(AccountType::Student)) {
            $data['filters']['idStudent'] = AuthManager::getAccount()->getIdAccount();
        }

        // Convertir les strings en enumération
        if(isset($data['orderOptions'])) {
            $data['orderOptions']['sortOrder'] = SortOrder::from($data['orderOptions']['sortOrder']);
        }
        if(isset($data['filters']['state'])) {
            $data['filters']['state'] = StateJustif::from($data['filters']['state']);
        }
    }

    /**
     * Renvoie une exception, si les données ne sont pas au format suivant :
     *
     *  - Une clé "absences", de type map, contenant des clés représentant une absence.
     *      - Chaque clé d'absence est au format "idStudent_timestamp" où timestamp est au format (YYYY-mm-dd HH:mm:ss)
     *      - Chaque clé d'absence contient une map au format suivant :
     *          - Une clé "state", de type string avec comme valeur "Validated" ou "Refused"
     *          - Une clé lock, de type boolean
     *  - Une clé "rejectionReason", de type string pouvant être vide
     *
     * @param array $data
     * @return void
     * @throws NestedValidationException
     */
    public static function validationPutDetailJustification(array &$data): void {
        $validator = v::key(
            'absences',
            v::arrayType()->each(
                v::key('state', v::in(['Validated', 'Refused']))
                ->key('lock', v::boolType())
            )->call('array_keys', v::each(new CompactAbsence()))
        )->key('rejectionReason', v::stringType());

        $validator->assert($data);

        // Pour chaque absence, remplace le string state par un object StateAbs
        foreach($data['absences'] as $key => $value) {
            $data['absences'][$key]['state'] = StateAbs::from($value['state']);

            // Si on valide l'absence, elle est forcément lock
            if($value['state'] == 'Validated') {
                $data['absences'][$key]['lock'] = true;
            }
        }
    }
}