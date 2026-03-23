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