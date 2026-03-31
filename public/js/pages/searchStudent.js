import { fetchStudents } from "../features/students/students.service.js";
import { renderStudents } from "../features/students/students.renderer.js";
import { showLoader } from "../core/utils.js";

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
        container.innerHTML = `
            <div class="d-flex flex-column align-items-center justify-content-center h-100">
                <p class="fs-1 text-body-secondary p-3">Erreur lors du chargement des étudiants</p>
            </div>`
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
    document.querySelectorAll(".apply-filters").forEach(btn => {
        btn.addEventListener("click", () => applyFilters());
    });
}

/**
 * Initialisation globale pour la page
 */
document.addEventListener("DOMContentLoaded", () => {
    initEvents();
    loadStudents();
});
