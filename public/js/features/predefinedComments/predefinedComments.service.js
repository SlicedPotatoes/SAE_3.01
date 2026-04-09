import { fetchAPI, sendJsonAPI } from "../../core/api.js";

/**
 * Récupère tous les commentaires prédéfinis
 *
 * @returns {Promise<*>}
 */
export async function fetchComments() {
    return fetchAPI("/api/predefinedComment");
}

/**
 * Crée un nouveau commentaire prédéfini
 *
 * @param {string} textComment
 * @returns {Promise<void>}
 */
export async function createComment(textComment) {
    return sendJsonAPI("/api/predefinedComment", "POST", JSON.stringify({ textComment }));
}

/**
 * Met à jour un commentaire prédéfini
 *
 * @param {number} id
 * @param {string} textComment
 * @returns {Promise<void>}
 */
export async function updateComment(id, textComment) {
    return sendJsonAPI(`/api/predefinedComment/${id}`, "PUT", JSON.stringify({ textComment }));
}

/**
 * Supprime un commentaire prédéfini
 *
 * @param {number} id
 * @returns {Promise<void>}
 */
export async function deleteComment(id) {
    return sendJsonAPI(`/api/predefinedComment/${id}`, "DELETE", null);
}
