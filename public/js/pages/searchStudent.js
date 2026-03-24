import { fetchStudents } from "../features/students/students.service.js";
import { renderStudents } from "../features/students/students.renderer.js";
import { showLoader } from "../core/utils.js";

let debounceTimer;

/**
 * Permet de charger les étudiants depuis l'api et de lancer les fonctions d'affichage
 *
 * @param query
 * @returns {Promise<void>}
 */
async function loadStudents(query = "") {
    const container = document.getElementById("studentsList");
    showLoader(container);

    try {
        const students = await fetchStudents(query);
        renderStudents(container, students);
    } catch (error) {
        console.error(error);
        container.innerHTML = `<div class="text-center p-3">Erreur lors du chargement</div>`;
    }
}

/**
 * Fonction d'application des filtres de recherche
 */
function applyFilters() {
    const search = document.getElementById("searchInput").value.trim();
    const group  = document.getElementById("groupSelect").value;

    const params = new URLSearchParams();
    if (search) params.append("search", search);
    if (group)  params.append("groupStudent", group);

    loadStudents(params.toString());
}

/**
 * Fonction qui permet d'initialiser les différents événements
 */
function initEvents() {
    const searchInput = document.getElementById("searchInput");
    const groupSelect = document.getElementById("groupSelect");

    // Frappe → debounce 500ms
    searchInput.addEventListener("input", () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(applyFilters, 500);
    });

    // Entrée → immédiat
    searchInput.addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
            clearTimeout(debounceTimer);
            applyFilters();
        }
    });

    // Changement groupe → immédiat
    groupSelect.addEventListener("change", () => {
        clearTimeout(debounceTimer);
        applyFilters();
    });
}

/**
 * Initialisation globale pour la page
 */
document.addEventListener("DOMContentLoaded", () => {
    initEvents();
    loadStudents();
});
