import { fetchAPI, sendJsonAPI } from "../../core/api.js";

/**
 * Récupère toutes les périodes de vacances
 *
 * @returns {Promise<*>}
 */
export async function fetchHolidays() {
    return fetchAPI("/api/holidays");
}

/**
 * Crée une nouvelle période de vacances
 *
 * @param {string} label
 * @param {string} startDate  format YYYY-MM-DD
 * @param {string} endDate    format YYYY-MM-DD
 * @returns {Promise<void>}
 */
export async function createHoliday(label, startDate, endDate) {
    return sendJsonAPI("/api/holidays", "POST", JSON.stringify({ label, startDate, endDate }));
}

/**
 * Met à jour une période de vacances
 *
 * @param {number} id
 * @param {string} label
 * @param {string} startDate  format YYYY-MM-DD
 * @param {string} endDate    format YYYY-MM-DD
 * @returns {Promise<void>}
 */
export async function updateHoliday(id, label, startDate, endDate) {
    return sendJsonAPI(`/api/holidays/${id}`, "PUT", JSON.stringify({ label, startDate, endDate }));
}

/**
 * Supprime une période de vacances
 *
 * @param {number} id
 * @returns {Promise<void>}
 */
export async function deleteHoliday(id) {
    return sendJsonAPI(`/api/holidays/${id}`, "DELETE", null);
}
