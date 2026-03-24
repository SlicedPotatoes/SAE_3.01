import { fetchTimeSlots } from "../features/timeslots/timeslots.service.js";
import { renderTimeSlots } from "../features/timeslots/timeslots.renderer.js";

import { isMobile, showLoader } from "../core/utils.js";

// Variable de caches pour les fetchs de l'api
let timeslotsData = [];

/**
 * Permet de charger les timeslots depuis l'api et de lancer les fonctions d'affichage
 *
 * @param query
 * @returns {Promise<void>}
 */
async function loadTimeSlots(query = "") {
    const container = document.getElementById("timeslotContainer");
    showLoader(container);

    try {
        timeslotsData = await fetchTimeSlots(query);

        renderTimeSlots(container, timeslotsData, false);

    } catch (error) {
        console.error(error);
        container.innerHTML = `<div class="text-center p-3">Erreur</div>`;
    }
}

/**
 * Fonctions de gestion des filtres pour les timeslotS
 */
function applyFilters() {
    const dateStart = document.getElementById("timeslotDateStart")?.value;
    const dateEnd = document.getElementById("timeslotDateEnd")?.value;
    const exam = document.getElementById("examFilter")?.checked;

    const params = new URLSearchParams();

    if (dateStart) params.append("dateStartFilter", dateStart);
    if (dateEnd) params.append("dateEndFilter", dateEnd);
    if (exam) params.append("examFilter", "true");

    loadTimeSlots(params.toString());
}

/**
 * Fonction qui permet d'initialisé les différents evenements
 */
function initEvents() {

    // Filtres
    document.querySelectorAll(".apply-filters").forEach(btn => {
        btn.addEventListener("click", () => {

            applyFilters();

            if (isMobile()) {
                const collapse = btn.closest(".collapse");
                bootstrap.Collapse.getInstance(collapse)?.hide();
            }
        });
    });
}

/**
 * Permet de gérer les changements d'affichage lié à la réduction ou aggrandissement des fenêtres
 */
let resizeTimer;

function initResizeHandler() {
    window.addEventListener("resize", () => {
        clearTimeout(resizeTimer);

        resizeTimer = setTimeout(() => {
            renderTimeSlots(
                document.getElementById("timeslotContainer"),
                timeslotsData
            );
        }, 150);
    });
}

/**
 * Initialisation global pour la page
 */
document.addEventListener("DOMContentLoaded", () => {
    initEvents();
    initResizeHandler();

    loadTimeSlots();
});