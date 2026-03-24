/**
 * Script detail-justification, vue RP quand le justificatif n'est pas traité.
 *
 * - Evenement changement d'état d'une absence individuel
 * - Evenement changement d'état de toute les absences
 * - Evenement gestion des commentaires prédéfinis
 * - Gestion des indicateurs demandant au responsable pédagogique de fournir un commentaire en cas de refus sur une absence
 * - Submit du formulaire
 */
import {updateAll as updateAllAbsences, initAbsence, updateState, updateLock, haveAtLeastOneRefused, absences} from "./state.js";
import {commentUpdate, visualUpdate, visualUpdateAll} from "../../features/process-justification/process-justification.renderer.js";
import {showSlide} from "./main.js";
import {initRequiredIndicatorField} from "../../core/validation-form.js";
import {processJustification as processJustificationAPI} from "../../features/process-justification/process-justification.service.js"
import {HttpError} from "../../core/api.js";
import {addNotification} from "../../core/notifications.js";
import {showFullScreenLoader} from "../../core/utils.js";

const domElements = {};
const requiredCommentIndicator = document.getElementById('requiredCommentIndicator');

const predefinedComment = document.getElementById('predefinedComments');
const comment = document.getElementById('comment');
const idJustification = document.getElementById('idJustification');

/**
 * Créer la structure de domElements
 * @param key clé représentant une absence
 * @param el element du DOM
 */
function initDomElements(key, el) {
    if(!domElements[key]) {
        domElements[key] = {
            'state': [],
            'lock': []
        };
    }
    domElements[key][el.dataset.key].push(el);
}

// Initialisation des absences, de domElements et event click pour la gestion d'une absence
document.querySelectorAll('[data-absence]').forEach(el => {
    const key = el.dataset.absence;
    initAbsence(key);
    initDomElements(key, el);

    el.addEventListener('click', () => {
        // Change state value
        if(el.dataset.key === 'state') {
            updateState(key);
        }
        // Change lock value
        else {
            updateLock(key);
        }
        visualUpdate(key, domElements);
    })
});
visualUpdateAll(domElements);

// Event updateAll
document.querySelectorAll('[data-update-all-action]').forEach((el) => {
    el.addEventListener('click', () => {
        const action = el.dataset.updateAllAction;
        const value = action === 'lock' ? (el.dataset.updateAllValue === 'true') : el.dataset.updateAllValue;

        updateAllAbsences(action, value);
        visualUpdateAll(domElements);
    });
});

// Gestion des commentaires prédéfinis
if(predefinedComment) {
    predefinedComment.addEventListener('change', (e) => {
        const value = e.target.value;

        if(value === '') return;

        if(comment.value.trim() === '') {
            comment.value = value;
        }
        else {
            comment.value += '\n' + value;
        }

        e.target.value = '';
    })
}

/**
 * Ajoute une update du DOM avant d'exécuté showSlide
 *
 * @param index
 * @returns {int}
 */
export function showCommentSection(index) {
    commentUpdate(requiredCommentIndicator, comment, haveAtLeastOneRefused());
    initRequiredIndicatorField(comment);

    return showSlide(index);
}

/**
 * Process un justificatif
 */
export async function processJustification() {
    if(haveAtLeastOneRefused() && comment.value === '') {
        addNotification('Error', 'Au moins une absence a été refusée, veuillez saisir un commentaire.');
        return;
    }

    try {
        showFullScreenLoader();
        await processJustificationAPI(idJustification.value, absences, comment.value);
        window.location.reload();
    }
    catch (e) {
        if(e instanceof HttpError) {
            e.body['messages'].forEach(error => {
                addNotification(error['type'], error['message']);
            })
        }
        else {
            addNotification('Error', 'Une erreur inattendue s\'est produite. Réessayez dans un instant.');
            console.log(e);
        }
    }
}