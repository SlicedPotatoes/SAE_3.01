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
export function renderTimeSlots(container, data, showTeacher = false) {
    if (!data || data.length === 0) {
        container.innerHTML = `
            <div class="text-center p-3">
                Aucun créneau
            </div>
        `;
        return;
    }

    if (isMobile()) {
        renderMobile(container, data, showTeacher);
    } else {
        renderDesktop(container, data, showTeacher);
    }
}

/**
 * Fonction PRIVEE qui gère l'affichage sur Desktop
 */
function renderDesktop(container, timeslots, showTeacher) {
    let html = `
        <table id="timeslotTable" 
        class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Ressource</th>
                    <th>Groupe</th>
                    ${showTeacher ? "<th>Professeur</th>" : ""}
                    <th>Absences</th>
                    <th>Justifiées</th>
                    <th>Examen</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
    `;

    timeslots.forEach((slot, index) => {
        html += `
            <tr data-bs-target="#slot-detail-${index}">
            
                <td>${formatDate(slot.time)} à ${formatTime(slot.time)}</td>

                <td> ${slot.resource?.label || ""} ${slot.courseType || ""} </td>
            
                <td> ${slot.group || "Non renseigné"} </td>
                
            ${showTeacher ? `<td>${slot.teacher?.firstName ? slot.teacher.firstName + " " + slot.teacher.lastName : "Non renseigné"}</td>` : ""}
            
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
                
                <td>
                    <a href="/absences-a-un-cours/${slot.teacher.idAccount}/${slot.resource.idResource}/${slot.group}/${slot.time}" 
                    class="btn btn-uphf">
                            Voir les détails
                    </a>
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
function renderMobile(container, timeslots, showTeacher) {
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

                        ${showTeacher ? `<div><strong>Professeur :</strong>${slot.teacher?.firstName ? slot.teacher.firstName + " " + slot.teacher.lastName : "Non renseigné"}</div>`
                        : ""}

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
                        
                        <div class="d-grid">
                            <a href="/absences-a-un-cours/${slot.teacher.idAccount}/${slot.resource.idResource}/${slot.group}/${slot.time}" 
                            class="btn btn-uphf">
                                Voir les détails
                            </a>
                        </div>

                    </div>

                </div>

            </div>
        `;
    });

    html += `</div>`;
    container.innerHTML = html;
}