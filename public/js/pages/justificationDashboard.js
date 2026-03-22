import {fetchJustificationsToDo, fetchJustificationsDone } from "../features/justifications/justifications.service.js";

import { renderJustifications } from "../features/justifications/justifications.renderer.js";
import { isMobile, showLoader } from "../core/utils.js";

// Variables de caches pour les fetchs de l'api
let justificationsToDoData = [];
let justificationsDoneData = [];


/**
 * Permet de charger les justificatifs à traité par le RP depuis l'api et de lancer les fonctions d'affichage
 *
 * @param query
 * @returns {Promise<void>}
 */
async function loadJustificationsToDo(query = "") {
    const container = document.getElementById("justificationContainer");
    showLoader(container);

    try {
        justificationsToDoData = await fetchJustificationsToDo(query);

        renderJustifications(container, justificationsToDoData, {
            showStudent: true,
            emptyMessage: "Aucun justificatif à traiter"
        });
    } catch (error) {
        console.error(error);
        container.innerHTML = `<div class="text-center p-3">Erreur</div>`;
    }
}

/**
 * Permet de charger les justificatifs qui ont était traités par le RP depuis l'api et de lancer les fonctions d'affichage
 *
 * @param query
 * @returns {Promise<void>}
 */
async function loadJustificationsDone(query = "") {
    const container = document.getElementById("proofDoneContainer");
    showLoader(container);
    try {
        justificationsDoneData = await fetchJustificationsDone(query);

        renderJustifications(container, justificationsDoneData, {
            showStudent: true,
            emptyMessage: "Aucun justificatif traité"
        });
    } catch (error) {
        console.error(error);
        container.innerHTML = `<div class="text-center p-3">Erreur</div>`;
    }
}


/**
 * Fonction d'application des filtres sur les différrents onglets de la page
 *
 * @param tab
 */
function applyFilters(tab) {
    const dateStart = document.getElementById(`${tab}DateStart`)?.value;
    const dateEnd = document.getElementById(`${tab}DateEnd`)?.value;

    const params = new URLSearchParams();

    if (dateStart) params.append("filters[dateStart]", dateStart);
    if (dateEnd) params.append("filters[dateEnd]", dateEnd);

    console.log(params.toString());

    if (tab === "todo") {
        loadJustificationsToDo(params.toString());
    }

    if (tab === "done") {
        loadJustificationsDone(params.toString());
    }
}


/**
 * Fonction permettant d'initialisé les différent évenements
 */
function initEvents() {
    // Navigation mobile
    const navButtons = document.querySelectorAll(".btn-nav");

    navButtons.forEach(btn => {
        btn.addEventListener("click", () => {
            navButtons.forEach(b => b.classList.remove("active"));
            btn.classList.add("active");
        });
    });

    // Permet de faire la liasion entre les tabs sur desktop et les boutons sur mobiles
    document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
        tab.addEventListener("shown.bs.tab", (event) => {
            const target = event.target.getAttribute("data-bs-target");

            document.querySelectorAll(".btn-nav").forEach(btn => {
                btn.classList.remove("active");

                if (btn.getAttribute("data-bs-target") === target) {
                    btn.classList.add("active");
                }
            });
        });
    });

    // Filtres
    document.querySelectorAll(".apply-filters").forEach(btn => {
        btn.addEventListener("click", () => {
            const tab = btn.dataset.tab;

            applyFilters(tab);
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
            renderJustifications(
                document.getElementById("justificationContainer"),
                justificationsToDoData,
                { showStudent: true }
            );

            renderJustifications(
                document.getElementById("proofDoneContainer"),
                justificationsDoneData,
                { showStudent: true }
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

    loadJustificationsToDo();
    loadJustificationsDone();
});