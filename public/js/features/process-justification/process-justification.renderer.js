import {absences, STATE_VALIDATED} from "../../pages/detail-justification/state.js";

/**
 * Fonction pour mettre à jour visuellement les éléments à partir de l'état interne de celui-ci.
 *
 * Pour un état, il y a deux éléments à mettre jour, celui de la version desktop, et de la version mobile.
 *
 * @param key clé représentant une absence
 * @param domElements
 */
export function visualUpdate(key, domElements) {
    const absence = absences[key];

    // Toggle état
    domElements[key]['state'].forEach(el => {
        el.checked = absence['state'] === STATE_VALIDATED;
    });

    // Bouton lock
    domElements[key]['lock'].forEach(el => {
        el.disabled = absence['state'] === STATE_VALIDATED;

        if(absence['lock']) {
            el.classList.replace('btn-success', 'btn-danger');
            el.querySelector('i').classList.replace('bi-unlock', 'bi-lock');
        }
        else {
            el.classList.replace('btn-danger', 'btn-success');
            el.querySelector('i').classList.replace('bi-lock', 'bi-unlock');
        }
    });
}

/**
 * Fonction pour mettre à jour le visuel de tous les éléments
 *
 * @param domElements
 */
export function visualUpdateAll(domElements) {
    Object.keys(absences).forEach(absenceKey => {
        visualUpdate(absenceKey, domElements);
    });
}

/**
 * Dans le cas où il y a au moins une absence refusée, affiche l'indicateur et met le champ comment en required
 * Dans le cas où il n'y a pas d'absence refusée, cache l'indicateur et met enleve required de comment
 *
 * @param requiredCommentIndicator
 * @param comment
 * @param atLestOneAbsenceRefused
 */
export function commentUpdate(requiredCommentIndicator, comment, atLestOneAbsenceRefused) {
    requiredCommentIndicator.classList.toggle('d-none', !atLestOneAbsenceRefused);
    comment.required = atLestOneAbsenceRefused;
}