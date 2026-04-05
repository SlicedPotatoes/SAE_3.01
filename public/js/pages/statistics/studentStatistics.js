import {hideFullScreenLoader, isMobile, showFullScreenLoader} from "../../core/utils.js";
import {canvasList, getTitleStatistics, moveFilter} from "./commonStatistics.js";
import {buildChart} from "../../features/statistics/statistics.renderer.js";
import {getStatistics} from "../../features/statistics/statistics.service.js";
import {addNotification} from "../../core/notifications.js";
import {HttpError} from "../../core/api.js";

const filter = document.getElementById('filter');
const desktopSlot = document.getElementById('filter-desktop');
const mobileSlot = document.getElementById('filter-mobile');

const mediaQuery = window.matchMedia('(width < 992px)');

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
 * Construire la query à partir des filtres
 *
 * @param studentFilter {boolean} - Indique si on doit inclure le filtre pour l'étudiant
 * @returns {string}
 */
function getQuery(studentFilter) {
    const state = document.getElementById("statisticsState").value;
    const group  = document.getElementById("statisticsGroups").value;
    const exam = document.getElementById("examFilter").checked;

    const params = new URLSearchParams();
    if (state && state !== '') params.append("state", state);
    if (group && group !== '')  params.append("group", group);
    if(exam) params.append("examen", "true");

    if(studentFilter) {
        params.append('idStudent', ID_STUDENT);
    }

    return params.toString();
}

/**
 * Récupération des données, et mise à jour de l'affichage
 * @returns {Promise<void>}
 */
async function loadStatistics() {
    try {
        showFullScreenLoader();
        dataAPI['general'] = (await getStatistics(getQuery(false)))['data'];
        dataAPI['student'] = (await getStatistics(getQuery(true)))['data'];

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
        moveFilter(filter, desktopSlot, mobileSlot);
        updateDisplay();
    });

    moveFilter(filter, desktopSlot, mobileSlot);
    await loadStatistics();
});