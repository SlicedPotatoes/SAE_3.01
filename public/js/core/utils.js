/**
 *
 * Les fonctions utilitaires utilisé dans les différentes autres fonctions javascript
 *
 */

/**
 * Permets de savoir si l'utilisateur à sa fenêtre en format mobile
 *
 * @param width Définie la borne pour être considéré comme mobile (default 768px)
 * @returns {boolean}
 */
export function isMobile(width = 768) {
    return window.innerWidth < width;
}

/**
 * Permet de prendre une date sous string brute et de la traduire en un string sous format français
 *
 * Exemple : "2026-03-20" -> "20/03/2026"
 *
 * @param dateString
 * @returns {string}
 */
export function formatDate(dateString) {
    const d = new Date(dateString);

    return d.toLocaleDateString("fr-FR");
}

/**
 * Permets d'avoir l'heure avec une date complète issu de l'api
 *
 * Exemple : "2026-03-20T15:55:00" -> "15h55"
 *
 * @param dateString
 * @returns {string}
 */
export function formatTime(dateString) {
    const d = new Date(dateString);

    const hours = String(d.getHours()).padStart(2, "0");
    const minutes = String(d.getMinutes()).padStart(2, "0");

    return `${hours}h${minutes}`;
}

/**
 * Permet de formater une durée issue de l'api
 *
 * Exemple : "01:30" -> "2h30"
 *
 * @param duration
 * @returns {string}
 */
export function formatDuration(duration) {
    const [h,m] = duration.split(":");

    return parseInt(h) + "h" + m;
}

/**
 * Permet de formater une date issu de l'api d'une manière plus lisible
 *
 * Exemple : "2026-03-20T15:55:00" -> "20/03/2026 15:55"
 *
 * @param dateString
 * @returns {string}
 */
export function formatDateTime(dateString) {
    const d = new Date(dateString);

    return d.toLocaleString("fr-FR");
}

/**
 * Permet de convertir un state php en classe Bootstrap pour les petits badges
 *
 * @param state
 * @returns {string}
 */
export function stateToBadge(state) {
    switch(state) {
        case "Processed":
        case "Validated":
            return "success";

        case "Refused":
        case "NotJustified":
            return "danger";

        default:
            return "secondary";
    }
}

/**
 * Permet de traduire les state php en label pour les petits badges
 *
 * @param state
 * @returns {*|string}
 */
export function translateLabelState(state) {
    switch(state) {
        case "Validated":
            return "Validée";
        case "Refused":
            return "Refusée";
        case "NotJustified":
            return "Non justifiée";
        case "Pending":
            return "En attente";
        case "Processed":
            return "Traité";
        case "NotProcessed":
            return "En cours de traitement";
        default:
            return state;
    }
}

/**
 * Permet d'afficher le loader
 * Utiliser lors du chargement des données pour permettre un feedback utilisateur
 *
 * @param container
 */
export function showLoader(container) {
    container.innerHTML = `
        <div class="d-flex justify-content-center align-items-center h-100">
            <div class="spinner-border"
                 style="width: 3.5rem; height: 3.5rem;"
                 role="status">
            </div>
        </div>
    `;
}

/**
 * Permet l'affichage du loader fullscreen
 */
export function showFullScreenLoader() {
    document.getElementById('full-screen-loader').classList.remove('d-none');
}

/**
 * Permet de cacher le loader fullscreen
 */
export function hideFullScreenLoader() {
    document.getElementById('full-screen-loader').classList.add('d-none');
}

/**
 * Permet de changer le footer sur mobile
 */
export function fixFooterMobile() {
    const footer = document.getElementById("footer");

    if (!footer) return;

    if (window.innerWidth < 768) {
        footer.style.setProperty("margin-bottom", "70px", "important");
    } else {
        footer.style.setProperty("margin-bottom", "0px", "important");
    }
}