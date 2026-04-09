import { isMobile, formatDate } from "../../core/utils.js";

/**
 * Affiche la liste des périodes de vacances dans le container
 * tableau sur desktop, cartes sur mobile
 *
 * @param {HTMLElement} container
 * @param {Array} holidays
 */
export function renderHolidays(container, holidays) {
    if (!holidays || holidays.length === 0) {
        container.innerHTML = `
            <div class="d-flex flex-column align-items-center justify-content-center h-100">
                <p class="fs-4 text-body-secondary p-3">Aucune période enregistrée.</p>
            </div>`;
        return;
    }

    if (isMobile()) {
        renderMobile(container, holidays);
    } else {
        renderDesktop(container, holidays);
    }
}

/**
 * Affichage desktop : tableau
 */
function renderDesktop(container, holidays) {
    let html = `
        <table class="table table-hover rounded-top overflow-hidden align-middle">
            <thead class="table-uphf">
                <tr>
                    <th>Libellé</th>
                    <th>Date début</th>
                    <th>Date fin</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>`;

    holidays.forEach(h => {
        html += `
            <tr>
                <td>
                    <div class="text-truncate" style="max-width: 200px" title="${escapeHtml(h.periodName)}">
                        ${escapeHtml(h.periodName)}
                    </div>
                </td>
                <td>${formatDate(h.startDate)}</td>
                <td>${formatDate(h.endDate)}</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary bi bi-pencil-square"
                                data-action="edit"
                                data-id="${h.id}"
                                data-name="${escapeHtml(h.periodName)}"
                                data-start="${h.startDate}"
                                data-end="${h.endDate}"
                                title="Modifier">
                        </button>
                        <button class="btn btn-outline-danger bi bi-trash"
                                data-action="delete"
                                data-id="${h.id}"
                                title="Supprimer">
                        </button>
                    </div>
                </td>
            </tr>`;
    });

    html += `</tbody></table>`;
    container.innerHTML = html;
}

/**
 * Affichage mobile : cartes
 */
function renderMobile(container, holidays) {
    let html = `<div class="d-flex flex-column gap-3 px-1">`;

    holidays.forEach(h => {
        html += `
            <div class="card shadow-sm">
                <div class="card-body p-3">
                    <h6 class="fw-bold mb-2">${escapeHtml(h.periodName)}</h6>
                    <div class="small text-muted mb-2">
                        <i class="bi bi-calendar-event me-1"></i>
                        Du <strong>${formatDate(h.startDate)}</strong> au <strong>${formatDate(h.endDate)}</strong>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary btn-sm flex-fill"
                                data-action="edit"
                                data-id="${h.id}"
                                data-name="${escapeHtml(h.periodName)}"
                                data-start="${h.startDate}"
                                data-end="${h.endDate}">
                            <i class="bi bi-pencil-square me-1"></i>Modifier
                        </button>
                        <button class="btn btn-outline-danger btn-sm flex-fill"
                                data-action="delete"
                                data-id="${h.id}">
                            <i class="bi bi-trash me-1"></i>Supprimer
                        </button>
                    </div>
                </div>
            </div>`;
    });

    html += `</div>`;
    container.innerHTML = html;
}

/**
 * Échappe les caractères HTML
 *
 * @param {string} str
 * @returns {string}
 */
function escapeHtml(str) {
    return String(str)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}
