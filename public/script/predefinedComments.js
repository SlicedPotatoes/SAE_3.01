/**
 * Script JS pour la page de gestion des commentaires prédéfinis
 * Gère l'affichage en mode édition/affichage
 */

function toggleEdit(commentId) {
    const textDiv = document.getElementById('comment-text-' + commentId);
    const editDiv = document.getElementById('comment-edit-' + commentId);
    const actionsDiv = document.getElementById('comment-actions-' + commentId);
    textDiv.classList.toggle('d-none');
    editDiv.classList.toggle('d-none');
    actionsDiv.classList.toggle('d-none');
}