import {sendJsonAPI} from "../../core/api.js";

/**
 * Call API pour récupérer les informations des statistiques
 *
 * @param query
 * @returns {Promise<*|null>}
 */
export async function getStatistics(query = "") {
    return await sendJsonAPI(`/api/statistics?${query}`, 'GET');
}