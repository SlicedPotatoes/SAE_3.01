import {hideFullScreenLoader, isMobile, showFullScreenLoader} from "../../core/utils.js";
import {canvasList, getTitleStatistics, moveFilter, buildQuery} from "./commonStatistics.js";
import {buildChart} from "../../features/statistics/statistics.renderer.js";
import {getStatistics} from "../../features/statistics/statistics.service.js";
import {addNotification} from "../../core/notifications.js";
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

let dataAPI = {
    general: {},
    student: {}
};

/**
 * Mettre à jour l'affichage à partir des données de dataAPI
 */
function updateDisplay() {
    // general ou student
    Object.keys(dataAPI).forEach(targetStatistics => {
        // Types de statistiques
        Object.keys(dataAPI[targetStatistics]).forEach(key => {
            let idCanvas = `${key}-chart`;

            if(isMobile(992)) idCanvas += '-mobile';
            if(targetStatistics === 'student') idCanvas += '-student';

            if(!canvasList.hasOwnProperty(idCanvas)) {
                canvasList[idCanvas] = document.querySelector(`#${idCanvas}`);
            }

            const oldChart = Chart.getChart(idCanvas);
            if(oldChart) {
                oldChart.destroy();
            }

            let title = getTitleStatistics(key);
            if(targetStatistics === 'student')  title += ' (étudiant)';
            else title += ' (générale)';

            buildChart(canvasList[idCanvas], dataAPI[targetStatistics][key], title);
        });
    });
}

/**
 * Récupération des données, et mise à jour de l'affichage
 * @returns {Promise<void>}
 */
async function loadStatistics() {
    try {
        showFullScreenLoader();
        dataAPI['general'] = (await getStatistics(
            buildQuery(filters.state, filters.group, filters.exam, false)
        ))['data'];
        dataAPI['student'] = (await getStatistics(
            buildQuery(filters.state, filters.group, filters.exam, true)
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