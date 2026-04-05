import {addNotification} from "../../core/notifications.js";
import {getStatistics} from "../../features/statistics/statistics.service.js";
import {buildChart} from "../../features/statistics/statistics.renderer.js";
import {buildQuery, canvasList, getTitleStatistics, moveFilter} from "./commonStatistics.js";
import {hideFullScreenLoader, showFullScreenLoader, isMobile} from "../../core/utils.js";
import {HttpError} from "../../core/api.js";

const filtersDiv = document.getElementById('filter');
const desktopSlot = document.getElementById('filter-desktop');
const mobileSlot = document.getElementById('filter-mobile');

const mediaQuery = window.matchMedia('(width < 992px)');
const filters = {
    state: document.querySelector("#statisticsState"),
    group: document.querySelector("#statisticsGroups"),
    exam: document.querySelector("#examFilter")
};

let dataAPI = {};

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
        dataAPI = (await getStatistics(
            buildQuery(filters.state, filters.group, filters.exam)
        ))['data'];

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
 * Initialisation
 */
document.addEventListener("DOMContentLoaded", async () => {
    document.querySelector('.apply-filters').addEventListener('click', async () => {
        await loadStatistics();
    });

    mediaQuery.addEventListener('change', () => {
        moveFilter(filtersDiv, desktopSlot, mobileSlot);
        updateDisplay();
    });

    moveFilter(filtersDiv, desktopSlot, mobileSlot);
    await loadStatistics();
});