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