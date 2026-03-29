/**
 * Permet de définir la valeur du cookie cardOpen
 *
 * Utilisé pour afficher ou non les cartes sur le profil étudiants
 * @param value
 */
export function setCardShow(value) {
    const date = new Date();
    date.setTime(date.getTime() + (24*60*60*1000));
    document.cookie = `cardOpen=${value}; expires=${date.toUTCString()};`;
}

/**
 * Permet de définir la valeur du cookie hideRuleModal
 *
 * Utilisé pour afficher ou non le modal des règles.
 *
 * @param value
 */
export function setHideRuleModal(value) {
    const date = new Date();
    date.setTime(date.getTime() + (24*60*60*1000));
    document.cookie = `hideRuleModal=${value}; expires=${date.toUTCString()};`;
}