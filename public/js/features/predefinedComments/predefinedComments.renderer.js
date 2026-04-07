/**
 * Échappe les caractères HTML pour éviter les injections XSS
 *
 * @param {string} str
 * @returns {string}
 */
function escapeHtml(str) {
    return str
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

/**
 * Génère le HTML d'une ligne de commentaire
 *
 * @param {Object} comment
 * @returns {string}
 */
function commentRow(comment) {
    return `
    <div class="list-group-item" data-id="${comment.idComment}">
        <div class="d-flex justify-content-between align-items-center gap-2">

            <!-- Mode affichage -->
            <span class="flex-fill comment-display">${escapeHtml(comment.textComment)}</span>
            <div class="btn-group comment-actions">
                <button class="btn btn-outline-primary btn-sm" data-action="edit" title="Modifier">
                    <i class="bi bi-pencil-square"></i>
                </button>
                <button class="btn btn-outline-danger btn-sm" data-action="delete" title="Supprimer">
                    <i class="bi bi-trash"></i>
                </button>
            </div>

            <!-- Mode édition (caché par défaut) -->
            <div class="input-group comment-edit d-none">
                <input type="text" class="form-control" value="${escapeHtml(comment.textComment)}">
                <button class="btn btn-success" data-action="save" title="Valider">
                    <i class="bi bi-check-lg"></i>
                </button>
                <button class="btn btn-secondary" data-action="cancel" title="Annuler">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

        </div>
    </div>`;
}

/**
 * Bascule l'affichage d'une ligne entre mode lecture et mode édition
 *
 * @param {HTMLElement} row
 */
export function toggleEdit(row) {
    row.querySelector('.comment-display').classList.toggle('d-none');
    row.querySelector('.comment-actions').classList.toggle('d-none');
    row.querySelector('.comment-edit').classList.toggle('d-none');
}

/**
 * Affiche la liste des commentaires dans le container
 *
 * @param {HTMLElement} container
 * @param {Array} comments
 */
export function renderComments(container, comments) {
    if (comments.length === 0) {
        container.innerHTML = `
            <div class="d-flex flex-column align-items-center justify-content-center h-100">
                <p class="fs-4 text-body-secondary p-3">Aucun commentaire prédéfini enregistré.</p>
            </div>`;
        return;
    }

    container.innerHTML = `<div class="list-group">${comments.map(commentRow).join('')}</div>`;
}
