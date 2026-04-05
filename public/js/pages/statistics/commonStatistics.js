import {isMobile} from "../../core/utils.js";

export const canvasList = {};

/**
 * Permet de récupérer le titre d'un graphique à partir du type de données.
 *
 * @param key
 * @returns {string}
 */
export function getTitleStatistics(key) {
    switch (key) {
        case 'typeCourse':
            return 'Proportion par type de cours';
        case 'teacher':
            return 'Proportion par enseignant';
        case 'resource':
            return 'Proportion par ressource';
        case 'group':
            return 'Proportion par groupe';
        case 'state':
            return 'Proportion par état';
        case 'examen':
            return 'Proportion examen';
        default:
            throw new Error(`Clé de statistique inconnue : ${key}`);
    }
}

/**
 * Fonction pour placer les filtres dans le slot correspondant (mobile ou desktop)
 */
export function moveFilter(filter, desktopSlot, mobileSlot) {
    if(isMobile(992)) {
        mobileSlot.appendChild(filter);
    }
    else {
        desktopSlot.appendChild(filter);
    }
}

/**
 * Construire la query à partir des filtres
 *
 * @param state {HTMLSelectElement}
 * @param group {HTMLSelectElement}
 * @param exam {HTMLInputElement}
 * @param studentFilter {boolean}
 * @returns {string}
 */
export function buildQuery(state, group, exam, studentFilter = false) {
    const params = new URLSearchParams();

    if(state && state.value !== '') params.append('state', state.value);
    if(group && group.value !== '') params.append('group', group.value);
    if(exam && exam.checked) params.append('examen', 'true');

    if(studentFilter) params.append('idStudent', ID_STUDENT);

    return params.toString();
}

/**
 * Clean up l'instance d'un chart, s'il existe
 * @param id
 */
export function destroyChart(id) {
    const chart = Chart.getChart(id);
    if(chart) chart.destroy();
}