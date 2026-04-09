import { fetchComments, createComment, updateComment, deleteComment } from "../features/predefinedComments/predefinedComments.service.js";
import { renderComments, toggleEdit } from "../features/predefinedComments/predefinedComments.renderer.js";
import { showLoader } from "../core/utils.js";

/**
 * Permet de charger les commentaires depuis l'api et de lancer l'affichage
 *
 * @returns {Promise<void>}
 */
async function loadComments() {
    const container = document.getElementById("commentContainer");
    showLoader(container);

    try {
        const comments = await fetchComments();
        renderComments(container, comments);
    } catch (error) {
        console.error(error);
        container.innerHTML = `<div class="text-center p-3">Erreur lors du chargement des commentaires</div>`;
    }
}

/**
 * Initialise les événements de la page
 */
function initEvents() {
    const addBtn    = document.getElementById("addCommentBtn");
    const addInput  = document.getElementById("newCommentInput");
    const container = document.getElementById("commentContainer");

    // Ajout d'un commentaire
    addBtn.addEventListener("click", async () => {
        const text = addInput.value.trim();
        if (!text) return;

        try {
            await createComment(text);
            addInput.value = "";
            await loadComments();
        } catch (error) {
            console.error(error);
        }
    });

    // Ajout via touche Entrée
    addInput.addEventListener("keydown", (e) => {
        if (e.key === "Enter") addBtn.click();
    });

    // Délégation d'événements pour edit / save / cancel / delete
    container.addEventListener("click", async (e) => {
        const btn = e.target.closest("[data-action]");
        if (!btn) return;

        const row    = btn.closest("[data-id]");
        const id     = parseInt(row.dataset.id);
        const action = btn.dataset.action;

        if (action === "edit" || action === "cancel") {
            toggleEdit(row);
            return;
        }

        if (action === "save") {
            const text = row.querySelector(".comment-edit input").value.trim();
            if (!text) return;

            try {
                await updateComment(id, text);
                await loadComments();
            } catch (error) {
                console.error(error);
            }
            return;
        }

        if (action === "delete") {
            try {
                await deleteComment(id);
                await loadComments();
            } catch (error) {
                console.error(error);
            }
        }
    });
}

/**
 * Initialisation globale pour la page
 */
document.addEventListener("DOMContentLoaded", () => {
    initEvents();
    loadComments();
});
