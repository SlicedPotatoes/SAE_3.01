import {
    isMobile,
    formatDate,
    formatTime,
    formatDuration
} from "../../core/utils.js";

/**
 * Fonction PUBLIC permettant de render le contener des timeslots (le nom est toujours stupid)
 *
 * Il gère version desktop et mobile
 */
export function renderTimeSlots(container, data, options = {}) {
    const {
        emptyMessage = "Aucun créneau"
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
function renderDesktop(container, timeslots) {
    let html = `
        <table id="timeslotTable" 
        class="table table-hover align-middle pointer">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Heure</th>
                    <th>Durée</th>
                    <th>Absences</th>
                    <th>Justifiées</th>
                    <th>Examen</th>
                </tr>
            </thead>
            <tbody>
    `;

    timeslots.forEach((slot, index) => {
        html += `
            <tr class="cursor-pointer"
                data-bs-toggle="collapse"
                data-bs-target="#slot-detail-${index}">

                <td>${formatDate(slot.time)}</td>
                <td>${formatTime(slot.time)}</td>
                <td>${formatDuration(slot.duration)}</td>

                <td>
                    <span class="badge text-bg-danger">
                        ${slot.countStudentsAbsences}
                    </span>
                </td>
                
                <td>
                    <span class="badge text-bg-success">
                        ${slot.countStudentsAbsencesJustified}
                    </span>
                </td>

                <td>
                    ${slot.examen ? `<span class="badge text-bg-warning">Examen</span>` : ""}
                </td>
            </tr>

            <tr class="bg-light">
                <td colspan="6" class="p-0">

                    <div class="collapse"
                         data-bs-parent="#timeslotTable"
                         id="slot-detail-${index}">

                        <div class="p-2">

                            <p class="mb-1">
                                <strong>Professeur :</strong>
                                ${slot.teacher?.firstName ? slot.teacher.firstName + " " + slot.teacher.lastName : "Non renseigné"}
                            </p>

                            <p class="mb-1">
                                <strong>Ressource :</strong>
                                ${slot.resource?.label || "Non renseigné"}
                            </p>

                            <p class="mb-1">
                                <strong>Type de cours :</strong>
                                ${slot.courseType || "Non renseigné"}
                            </p>

                            <p class="mb-0">
                                <strong>Groupe :</strong>
                                ${slot.group || "Non renseigné"}
                            </p>

                        </div>

                    </div>

                </td>
            </tr>
        `;
    });

    html += "</tbody></table>";
    container.innerHTML = html;
}

/**
 * Fonction PRIVEE qui gère l'affichage sur Mobile
 */
function renderMobile(container, timeslots) {
    let html = `<div class="d-flex flex-column gap-3 px-2 mt-2">`;

    timeslots.forEach(slot => {
        html += `
            <div class="card shadow-sm">

                <div class="card-body p-3">
                    <div>
                        <p class="mb-0">
                            Le <strong>${formatDate(slot.time)}</strong>
                            à <strong>${formatTime(slot.time)}</strong>
                        </p>
                    </div>

                    <div class="mb-1">
                        <strong>Durée :</strong> ${formatDuration(slot.duration)}
                    </div>

                    <div class="mb-1">
                        <span class="badge text-bg-danger">
                            Absents : ${slot.countStudentsAbsences}
                        </span>
                        <span class="badge text-bg-success ms-2">
                            Justifiés : ${slot.countStudentsAbsencesJustified}
                        </span>
                        ${slot.examen ? `<span class="badge text-bg-warning ms-2">Examen</span>` : ""}
                    </div>

                    <hr class="my-2">

                    <div class="small">

                        <div>
                            <strong>Professeur :</strong>
                            ${slot.teacher?.firstName ? slot.teacher.firstName + " " + slot.teacher.lastName : "Non renseigné"}
                        </div>

                        <div>
                            <strong>Ressource :</strong>
                            ${slot.resource?.label || "Non renseigné"}
                        </div>

                        <div>
                            <strong>Type :</strong>
                            ${slot.courseType || "Non renseigné"}
                        </div>

                        <div>
                            <strong>Groupe :</strong>
                            ${slot.group || "Non renseigné"}
                        </div>

                    </div>

                </div>

            </div>
        `;
    });

    html += `</div>`;
    container.innerHTML = html;
}