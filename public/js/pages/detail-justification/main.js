/**
 * Script de la view detail-justification
 *
 * Il définit les actions + gestion d'événement des boutons d'action dans les slides
 */
import {showCommentSection, processJustification} from "./process-justification.js";

const slides = document.querySelectorAll('.slide');
let currentSlide = 0;

/**
 * Permet de changer de slide
 * @param next
 * @returns {*}
 */
export function showSlide(next) {
    if (next < 0 || next >= slides.length) {
        throw Error(`La slide ${next} n'existe pas`);
    }

    slides[currentSlide].style.setProperty('display', 'none', 'important');
    slides[next].style.removeProperty('display');

    currentSlide = next;
}

// Définition des actions possibles pour les boutons
const actions = {
    backToHome: () => window.location.href = '/',
    showSlide: (index) => showSlide(index),
    showCommentSection: (index) => showCommentSection(index),
    processJustification: () => processJustification()
}

// Initialisation des events pour effectuer l'action associée à un bouton des slides.
document.querySelectorAll('button[data-action]').forEach((el) => {
    el.addEventListener('click', () => {
        const action = actions[el.dataset.action];

        if(action === undefined) {
            throw Error(`L'action n'existe pas !`);
        }

        action(el.dataset.param);
    });
});