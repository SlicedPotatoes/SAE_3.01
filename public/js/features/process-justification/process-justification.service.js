import {sendJsonAPI} from "../../core/api.js";

/**
 * Call API pour le traitement d'un justificatif
 *
 * @param id
 * @param absences
 * @param comment
 * @returns {Promise<*|null>}
 */
export async function processJustification(id, absences, comment) {
    const data = {
        absences: absences,
        rejectionReason: comment
    };

    return await sendJsonAPI(`/api/justifications/${id}`, 'PUT', JSON.stringify(data));
}