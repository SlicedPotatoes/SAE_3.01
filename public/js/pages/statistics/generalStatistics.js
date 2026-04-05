import {addNotification} from "../../core/notifications.js";
import {getStatistics} from "../../features/statistics/statistics.service.js";
import {buildChart} from "../../features/statistics/statistics.renderer.js";
import {canvasList, getTitleStatistics, moveFilter} from "./commonStatistics.js";
import {hideFullScreenLoader, showFullScreenLoader, isMobile} from "../../core/utils.js";
import {HttpError} from "../../core/api.js";

const filter = document.getElementById('filter');
const desktopSlot = document.getElementById('filter-desktop');
const mobileSlot = document.getElementById('filter-mobile');

const mediaQuery = window.matchMedia('(width < 992px)');

let dataAPI = {};
let query = '';

/**
 * Mettre à jour l'affichage à partir des données de dataAPI
 */
function updateDisplay() {
    Object.keys(dataAPI).forEach(key => {
        const idCanvas = `${key}-chart${isMobile(992) ? '-mobile' : ''}`;

        if(!canvasList.hasOwnProperty(idCanvas)) {
            canvasList[idCanvas] = document.querySelector(`#${idCanvas}`);
        }

        const oldChart = Chart.getChart(idCanvas);
        if(oldChart) {
            oldChart.destroy();
        }

        buildChart(canvasList[idCanvas], dataAPI[key], getTitleStatistics(key));
    });
}

/**
 * Récupération des données, et mise à jour de l'affichage
 * @returns {Promise<void>}
 */
async function loadStatistics() {
    try {
        showFullScreenLoader();
        dataAPI = (await getStatistics(query))['data'];

        updateDisplay();
    }
    catch (error) {
        if(error instanceof HttpError) {
            console.log(error.body);
        }
        else {
            console.log(error);
        }

        addNotification('Error', 'Erreur lors du chargement des données');
    }

    hideFullScreenLoader();
}

/**
 * Création de query à partir des filtres, puis exécution de la requête avec mise à jour de l'affichage
 * @returns {Promise<void>}
 */
async function applyFilters() {
    const state = document.getElementById("statisticsState").value;
    const group  = document.getElementById("statisticsGroups").value;
    const exam = document.getElementById("examFilter").checked;

    const params = new URLSearchParams();
    if (state && state !== '') params.append("state", state);
    if (group && group !== '')  params.append("group", group);
    if(exam) params.append("examen", "true");

    query = params.toString();

    await loadStatistics();
}

/**
 * Initialisation
 */
document.addEventListener("DOMContentLoaded", async () => {
    document.querySelector('.apply-filters').addEventListener('click', async () => {
        await applyFilters();
    });

    mediaQuery.addEventListener('change', () => {
        moveFilter(filter, desktopSlot, mobileSlot);
        updateDisplay();
    });

    moveFilter(filter, desktopSlot, mobileSlot);
    await loadStatistics();
});