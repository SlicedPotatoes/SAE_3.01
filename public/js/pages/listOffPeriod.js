import { fetchHolidays, createHoliday } from "../features/holidays/holidays.service.js";
import { renderHolidays } from "../features/holidays/holidays.renderer.js";
import { showLoader } from "../core/utils.js";

// Variable de cache pour les fetchs de l'api
let holidaysData = [];

/**
 * Permet de charger les périodes de vacances depuis l'api et de lancer l'affichage
 *
 * @returns {Promise<void>}
 */
async function loadHolidays() {
    const container = document.getElementById("offPeriodContainer");
    showLoader(container);

    try {
        holidaysData = await fetchHolidays();
        renderHolidays(container, holidaysData);
    } catch (error) {
        console.error(error);
        container.innerHTML = `
            <div class="d-flex flex-column align-items-center justify-content-center h-100">
                <p class="fs-1 text-body-secondary p-3">Erreur lors du chargement des périodes</p>
            </div>`;
    }
}

/**
 * Fonction qui permet d'initialiser les différents événements
 */
function initEvents() {
    window.addEventListener("resize", () => {
        renderHolidays(document.getElementById("offPeriodContainer"), holidaysData);
    });

    document.getElementById("offPeriodSubmitBtn").addEventListener("click", async () => {
        const label     = document.getElementById("offPeriodName").value.trim();
        const startDate = document.getElementById("offPeriodStart").value;
        const endDate   = document.getElementById("offPeriodEnd").value;

        if (!label || !startDate || !endDate) return;

        try {
            await createHoliday(label, startDate, endDate);
            bootstrap.Modal.getInstance(document.getElementById("modalAddOffPeriod")).hide();
            await loadHolidays();
        } catch (error) {
            console.error(error);
        }
    });
}

/**
 * Initialisation globale pour la page
 */
document.addEventListener("DOMContentLoaded", () => {
    initEvents();
    loadHolidays();
});
