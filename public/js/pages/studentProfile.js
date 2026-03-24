import { fetchAbsences } from "../features/absences/absences.service.js";
import { fetchJustifications } from "../features/justifications/justifications.service.js";

import { renderAbsences } from "../features/absences/absences.renderer.js";
import { renderJustifications } from "../features/justifications/justifications.renderer.js";

import { isMobile, showLoader, fixFooterMobile } from "../core/utils.js";

// Variables de caches pour les fetchs de l'api
let absencesData = [];
let justificationsData = [];

/**
 * Permet de charger les absences depuis l'api et de lancer les fonctions d'affichage
 *
 * @param query
 * @returns {Promise<void>}
 */
async function loadAbsences(query = "") {
    const container = document.getElementById("absenceContainer");

    showLoader(container);

    try {
        const base = `idStudent=${STUDENT_ID}`;
        const params = new URLSearchParams(query);

        params.append("orderOptions[columns][]", "time");
        params.append("orderOptions[sortOrder]", "DESC");

        const finalQuery = `${base}&${params.toString()}`;

        absencesData = await fetchAbsences(finalQuery);
        renderAbsences(container, absencesData);
    } catch (error) {
        console.error(error);

        container.innerHTML = `
            <div class="text-center p-3">
                Erreur lors du chargement des absences
            </div>
        `;
    }
}

/**
 * Permet de charger les justificatifs depuis l'api et de lancer les fonctions d'affichage
 *
 * @param query
 * @returns {Promise<void>}
 */
async function loadJustifications(query = "") {
    const container = document.getElementById("justificationContainer");

    showLoader(container);

    try {
        const base = `filters[idStudent]=${STUDENT_ID}`;
        const params = new URLSearchParams(query);

        params.append("orderOptions[columns][]", "senddate")
        params.append("orderOptions[sortOrder]", "DESC")

        const finalQuery = `${base}&${params.toString()}`;

        justificationsData = await fetchJustifications(finalQuery);
        renderJustifications(container, justificationsData);
    } catch (error) {
        console.error(error);

        container.innerHTML = `
            <div class="text-center p-3">
                Erreur lors du chargement des justificatifs
            </div>
        `;
    }
}

/**
 * Fonctions de gestion des filtrs pour les absences afin d'utiliser facilement l'api
 */
function applyAbsenceFilters() {
    const dateStart = document.getElementById("absDateStart").value;
    const dateEnd = document.getElementById("absDateEnd").value;
    const exam = document.getElementById("absExam").checked;
    const locked = document.getElementById("absLocked")?.checked;

    const params = new URLSearchParams();

    if (dateStart) params.append("dateStart", dateStart);
    if (dateEnd) params.append("dateEnd", dateEnd);
    if (exam) params.append("examen", true);
    if (locked) params.append("lock", true);

    loadAbsences(params.toString());
}

/**
 * Fonctions de gestion des filtrs pour les justificatifs afin d'utiliser facilement l'api
 */
function applyJustificationFilters() {
    const dateStart = document.getElementById("proofDateStart").value;
    const dateEnd = document.getElementById("proofDateEnd").value;
    const state = document.getElementById("proofState")?.value;

    const params = new URLSearchParams();

    if (dateStart) params.append("filters[dateStart]", dateStart);
    if (dateEnd) params.append("filters[dateEnd]", dateEnd);
    if (state) params.append("filters[state]", state);



    console.log(params.toString())

    loadJustifications(params.toString());
}

/**
 * Fonction qui permet d'initialisé les différents evenements
 *
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

            const target = btn.dataset.target;

            if (target === "absence") applyAbsenceFilters();
            if (target === "justification") applyJustificationFilters();

            // fermeture mobile
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

            renderAbsences(
                document.getElementById("absenceContainer"),
                absencesData
            );

            renderJustifications(
                document.getElementById("justificationContainer"),
                justificationsData
            );

            fixFooterMobile();

        }, 150);
    });
}

/**
 * Initialisation global pour la page
 */
document.addEventListener("DOMContentLoaded", () => {

    fixFooterMobile();

    initEvents();
    initResizeHandler();

    loadAbsences();
    loadJustifications();
});