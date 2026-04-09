import { fetchHolidays, createHoliday, updateHoliday, deleteHoliday } from "../features/holidays/holidays.service.js";
import { renderHolidays } from "../features/holidays/holidays.renderer.js";
import { showLoader } from "../core/utils.js";
import { addNotification } from "../core/notifications.js";

// Variable de cache pour les fetchs de l'api
let holidaysData = [];

// ID de la période en cours de modification
let editingId = null;

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
        addNotification('Error', 'Erreur lors du chargement des périodes de vacances.');
        container.innerHTML = `
            <div class="d-flex flex-column align-items-center justify-content-center h-100">
                <p class="fs-4 text-body-secondary p-3">Erreur lors du chargement des périodes.</p>
            </div>`;
    }
}

/**
 * Recharge les périodes depuis l'api sans afficher le loader
 *
 * @returns {Promise<void>}
 */
async function reloadHolidays() {
    const container = document.getElementById("offPeriodContainer");
    try {
        holidaysData = await fetchHolidays();
        renderHolidays(container, holidaysData);
    } catch (error) {
        addNotification('Error', 'Erreur lors du chargement des périodes de vacances.');
    }
}

/**
 * Configure le modal en mode ajout ou modification
 *
 * @param {"add"|"edit"} mode
 * @param {Object} data - données à pré-remplir en mode edit
 */
function setModalMode(mode, data = {}) {
    document.getElementById("modalAddOffPeriodLabel").textContent =
        mode === "edit" ? "Modifier une période" : "Ajouter une période";
    document.getElementById("offPeriodSubmitBtn").textContent =
        mode === "edit" ? "Modifier" : "Ajouter";
    document.getElementById("offPeriodName").value  = data.name  ?? "";
    document.getElementById("offPeriodStart").value = data.start ?? "";
    document.getElementById("offPeriodEnd").value   = data.end   ?? "";
}

/**
 * Supprime une période de vacances et met à jour l'affichage localement
 *
 * @param {number} id
 * @returns {Promise<void>}
 */
async function handleDelete(id) {
    try {
        await deleteHoliday(id);
        holidaysData = holidaysData.filter(h => h.id !== id);
        renderHolidays(document.getElementById("offPeriodContainer"), holidaysData);
    } catch (error) {
        addNotification('Error', "Une erreur inattendue s'est produite, réessayez.");
    }
}

/**
 * Permet de gérer les changements d'affichage lié à la réduction ou aggrandissement des fenêtres
 */
let resizeTimer;

function initResizeHandler() {
    window.addEventListener("resize", () => {
        clearTimeout(resizeTimer);

        resizeTimer = setTimeout(() => {
            renderHolidays(
                document.getElementById("offPeriodContainer"),
                holidaysData
            );
        }, 150);
    });
}

/**
 * Fonction qui permet d'initialiser les différents événements
 */
function initEvents() {
    // Délégation sur le container pour les boutons edit / delete
    document.getElementById("offPeriodContainer").addEventListener("click", (e) => {
        const btn = e.target.closest("[data-action]");
        if (!btn) return;

        const action = btn.dataset.action;
        const id     = parseInt(btn.dataset.id);

        if (action === "delete") {
            handleDelete(id);
        } else if (action === "edit") {
            editingId = id;
            setModalMode("edit", {
                name:  btn.dataset.name,
                start: btn.dataset.start,
                end:   btn.dataset.end,
            });
            bootstrap.Modal.getOrCreateInstance(document.getElementById("modalAddOffPeriod")).show();
        }
    });

    // Réinitialisation du modal en mode ajout à la fermeture
    document.getElementById("modalAddOffPeriod").addEventListener("hidden.bs.modal", () => {
        editingId = null;
        setModalMode("add");
    });

    // Soumission du formulaire : ajout ou modification selon editingId
    document.getElementById("offPeriodSubmitBtn").addEventListener("click", async () => {
        const label     = document.getElementById("offPeriodName").value.trim();
        const startDate = document.getElementById("offPeriodStart").value;
        const endDate   = document.getElementById("offPeriodEnd").value;

        if (!label || !startDate || !endDate) {
            addNotification('Error', 'Veuillez remplir tous les champs.');
            return;
        }

        if (startDate > endDate) {
            addNotification('Error', 'La date de début doit être antérieure à la date de fin.');
            return;
        }

        const currentId = editingId;
        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById("modalAddOffPeriod"));

        try {
            if (currentId !== null) {
                await updateHoliday(currentId, label, startDate, endDate);
                holidaysData = holidaysData.map(h => h.id === currentId
                    ? { ...h, periodName: label, startDate, endDate }
                    : h
                );
                renderHolidays(document.getElementById("offPeriodContainer"), holidaysData);
            } else {
                await createHoliday(label, startDate, endDate);
                await reloadHolidays();
            }
            modal.hide();
        } catch (error) {
            addNotification('Error', "Une erreur inattendue s'est produite, réessayez.");
        }
    });
}

/**
 * Initialisation globale pour la page
 */
document.addEventListener("DOMContentLoaded", () => {
    initEvents();
    initResizeHandler();
    loadHolidays();
});
