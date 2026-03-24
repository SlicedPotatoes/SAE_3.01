import {
    isMobile,
    formatDate,
    formatTime,
    formatDuration,
    stateToBadge,
    translateLabelState
} from "../../core/utils.js";

/**
 * Fonction PUBLIC permettant de render le contener des absences
 *
 * Il gère version desktop et mobile
 */
export function renderAbsences(container, data, options = {}) {

    const {
        emptyMessage = "Aucune absence"
    } = options;

    if (!data || data.length === 0) {
        container.innerHTML = `
            <div class="text-center p-3">
                ${emptyMessage}
            </div>
        `;
        return;
    }

    if (isMobile()) {
        renderMobile(container, data);
    } else {
        renderDesktop(container, data);
    }
}

/**
 * Fonction PRIVEE qui gère l'affichage sur Desktop
 */
function renderDesktop(container, absences) {

    let html = `
        <table id="absenceTable" 
        class="table table-hover align-middle pointer">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Heure</th>
                    <th>Durée</th>
                    <th>État</th>
                    <th>Examen</th>
                </tr>
            </thead>
            <tbody>
    `;

    absences.forEach((abs, index) => {

        let lockIcon = "";

        if (!abs.allowedJustification && abs.currentState === "Refused") {
            lockIcon = `
                <i class="bi bi-file-lock2 ms-2"
                   style="font-size:20px"
                   data-bs-toggle="tooltip"
                   data-bs-title="Le responsable pédagogique n'autorise pas la justification">
                </i>`;
        }

        html += `
            <tr class="cursor-pointer"
                data-bs-toggle="collapse"
                data-bs-target="#abs-detail-${index}">

                <td>${formatDate(abs.time)}</td>
                <td>${formatTime(abs.time)}</td>
                <td>${formatDuration(abs.duration)}</td>

                <td>
                    <span class="badge rounded-pill text-bg-${stateToBadge(abs.currentState)}">
                        ${translateLabelState(abs.currentState)}
                    </span>
                    ${lockIcon}
                </td>

                <td>
                    ${abs.examen ? `<span class="badge text-bg-warning">Examen</span>` : ""}
                </td>
            </tr>

            <tr class="bg-light">
                <td colspan="5" class="p-0">

                    <div class="collapse"
                         data-bs-parent="#absenceTable"
                         id="abs-detail-${index}">

                        <div class="p-2">

                            <p class="mb-1">
                                <strong>Professeur :</strong>
                                ${abs.teacherFirstName
            ? abs.teacherFirstName + " " + abs.teacherLastName
            : "Non renseigné"}
                            </p>

                            <p class="mb-1">
                                <strong>Ressource :</strong>
                                ${abs.labelResource}
                            </p>

                            <p class="mb-0">
                                <strong>Type de cours :</strong>
                                ${abs.courseType}
                            </p>

                        </div>

                    </div>

                </td>
            </tr>
        `;
    });

    html += "</tbody></table>";

    container.innerHTML = html;

    initTooltips(container);
}

/**
 * Fonction PRIVEE qui gère l'affichage sur Mobile
 */
function renderMobile(container, absences) {

    let html = `<div class="d-flex flex-column gap-3 px-2 mt-2">`;

    absences.forEach(abs => {

        let lockIcon = "";

        if (!abs.allowedJustification && abs.currentState === "Refused") {
            lockIcon = `
                <i class="bi bi-file-lock2 ms-2 fs-4"
                   data-bs-toggle="tooltip"
                   data-bs-title="Le responsable pédagogique n'autorise pas la justification">
                </i>`;
        }

        html += `
            <div class="card shadow-sm">

                <div class="card-body p-3">

                    <div>
                        <p class="mb-0">
                            Le <strong>${formatDate(abs.time)}</strong>
                            à <strong>${formatTime(abs.time)}</strong>
                        </p>
                    </div>

                    <div class="mb-1">
                        <strong>Durée :</strong> ${formatDuration(abs.duration)}
                    </div>

                    <div class="mb-1">
                        <span class="badge rounded-pill text-bg-${stateToBadge(abs.currentState)}">
                            ${translateLabelState(abs.currentState)}
                        </span>
                        ${lockIcon}
                        ${abs.examen ? `<span class="badge text-bg-warning ms-2">Examen</span>` : ""}
                    </div>

                    <hr class="my-2">

                    <div class="small">

                        <div>
                            <strong>Professeur :</strong>
                            ${abs.teacherFirstName
            ? abs.teacherFirstName + " " + abs.teacherLastName
            : "Non renseigné"}
                        </div>

                        <div>
                            <strong>Ressource :</strong>
                            ${abs.labelResource}
                        </div>

                        <div>
                            <strong>Type de cours :</strong>
                            ${abs.courseType}
                        </div>

                    </div>

                </div>

            </div>
        `;
    });

    html += `</div>`;

    container.innerHTML = html;

    initTooltips(container);
}

/**
 * Fonction pour les tooltips de verrouillage d'absence
 * @param container
 */
function initTooltips(container) {
    const tooltipTriggerList = container.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipTriggerList.forEach(el => new bootstrap.Tooltip(el));
}