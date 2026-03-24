/**
 * Script gérant l'état interne des absences
 * @type {string}
 */

export const STATE_VALIDATED = 'Validated';
export const STATE_REFUSED = 'Refused';
export const absences = {};

/**
 * Permet de mettre à jour toutes les absences d'un coup à partir d'un attribut, et la valeur de celui-ci
 *
 * @param key Attribut à modifier
 * @param value Valeur à affecter pour cet attribut
 */
export function updateAll(key, value) {
    Object.keys(absences).forEach(id => {
        if(key === 'state' && value === STATE_VALIDATED) {
            absences[id]['lock'] = true;
        }

        if(key === 'lock' && absences[id]['state'] === STATE_VALIDATED) {
            return;
        }

        absences[id][key] = value;
    });
}

/**
 * Initialisé une absence
 * @param key
 */
export function initAbsence(key) {
    absences[key] = {state: STATE_REFUSED, lock: true};
}

/**
 * Mettre à jour l'état d'une absence
 * @param key
 */
export function updateState(key) {
    absences[key]['state'] = absences[key]['state'] === STATE_VALIDATED ? STATE_REFUSED : STATE_VALIDATED;
    absences[key]['lock'] = absences[key]['state'] === STATE_VALIDATED ? true : absences[key]['lock'];
}

/**
 * Mettre à jour le lock d'une absence
 * @param key
 */
export function updateLock(key) {
    absences[key]['lock'] = !absences[key]['lock'];
}

/**
 * Permet de savoir si au moins une absence a été refusé
 * @returns {boolean}
 */
export function haveAtLeastOneRefused() {
    let result = false;

    Object.keys(absences).forEach(absenceKey => {
        if(absences[absenceKey]['state'] === STATE_REFUSED) {
            result = true;
        }
    });

    return result;
}
