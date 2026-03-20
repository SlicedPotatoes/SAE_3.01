import {
    isMobile,
    formatDate,
    stateToBadge,
    translateLabelState
} from "../../core/utils.js";

/**
 * Fonction PUBLIC permettant de rander (afficher pour Kévin) le contener des justifications
 *
 * Il gère version desktop et mobile
 */
export function renderJustifications(container, data, options = {}) {

    const {
        showStudent = false,
        emptyMessage = "Aucun justificatif"
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
        renderMobile(container, data, showStudent);
    } else {
        renderDesktop(container, data, showStudent);
    }
}

/**
 * Fonction PRIVEE qui gère l'affichage sur Desktop
 */
function renderDesktop(container, justifications, showStudent) {

    let html = `
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    ${showStudent ? "<th>Étudiant</th>" : ""}
                    <th>Date début</th>
                    <th>Date fin</th>
                    <th class="text-center">État</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
    `;

    // Pour chaque justificatifs il créer une "lineJustification" sous forme d'un tableau
    justifications.forEach(j => {

        html += `
            <tr>

                ${showStudent ? `
                    <td class="text-nowrap">
                        ${j.student?.firstName ?? ""} ${j.student?.lastName ?? ""}
                    </td>
                ` : ""}

                <td class="text-nowrap">
                    ${formatDate(j.startDate)}
                </td>

                <td class="text-nowrap">
                    ${formatDate(j.endDate)}
                </td>

                <td class="text-center">
                    <span class="badge rounded-pill text-bg-${stateToBadge(j.currentState)}">
                        ${translateLabelState(j.currentState)}
                    </span>
                </td>

                <td class="text-center">
                    <a href="/DetailJustification/${j.idJustification}"
                       class="btn btn-sm btn-uphf">
                        Voir les détails
                    </a>
                </td>

            </tr>
        `;
    });

    html += `
            </tbody>
        </table>
    `;

    container.innerHTML = html;
}

/**
 * Fonction PRIVEE qui gère l'affichage sur Mobile
 */
function renderMobile(container, justifications, showStudent) {

    let html = `<div class="d-flex flex-column gap-3 px-2 mt-2">`;

    // Pour chaque justificatifs il créer une "lineJustification" sous forme d'une card
    justifications.forEach(j => {

        html += `
            <div class="card shadow-sm">

                <div class="card-body p-3">

                    ${showStudent ? `
                        <div class="mb-2">
                            <strong>Étudiant :</strong>
                            ${j.student?.firstName ?? ""} ${j.student?.lastName ?? ""}
                        </div>
                    ` : ""}

                    <div class="d-flex justify-content-between mb-2">
                        <span>
                            <strong>Début :</strong> ${formatDate(j.startDate)}
                        </span>

                        <span>
                            <strong>Fin :</strong> ${formatDate(j.endDate)}
                        </span>
                    </div>

                    <div class="mb-3">
                        <span class="badge rounded-pill text-bg-${stateToBadge(j.currentState)}">
                            ${translateLabelState(j.currentState)}
                        </span>
                    </div>

                    <div class="d-grid">
                        <a href="/DetailJustification/${j.idJustification}"
                           class="btn btn-uphf">
                            Voir les détails
                        </a>
                    </div>

                </div>

            </div>
        `;
    });

    html += `</div>`;

    container.innerHTML = html;
}