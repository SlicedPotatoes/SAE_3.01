<?php
/**
 * Page du "dashboard étudiant", permettant d'observer les absences et justificatifs d'un étudiant
 */
?>

<div class="card p-3 flex-fill d-flex flex-column" style="min-height:0">

    <!-- Tab bar -->
    <ul class="nav nav-tabs"
        id="tab-dashboard-stu"
        role="tablist">

        <li class="nav-item"
            role="presentation">
            <button
                class="text-black nav-link active"
                id="proof-tab"
                data-bs-toggle="tab"
                data-bs-target="#proof-tab-pane"
                type="button"
                role="tab">
                Justificatifs
            </button>
        </li>

        <li class="nav-item"
            role="presentation">
            <button
                class="text-black nav-link"
                id="absence-tab"
                data-bs-toggle="tab"
                data-bs-target="#absence-tab-pane"
                type="button"
                role="tab">
                Absences
            </button>
        </li>

        <li class="ms-auto">
            <?php require __DIR__ . "/../ViewOLD/Composants/Modal/modalJustificationAbsence.php"; ?>
        </li>
    </ul>

    <div class="tab-content bg-white border-bottom border-start border-end rounded-bottom pt-3 flex-fill d-flex flex-column"
         style="min-height:0"
         id="tab-dashboard-stuContent">

        <!-- TAB JUSTIFICATIFS -->
        <div class="tab-pane fade show active h-100"
             id="proof-tab-pane"
             role="tabpanel">

            <div class="d-flex flex-column h-100"
                 style="min-height:0">

                <?php require __DIR__ . "/Component/filters/filterJustification.php"; ?>

                <div id="justificationContainer"
                     class="flex-fill overflow-y-auto"
                     role="status"
                     style="min-height:0">
                    Chargement de données...
                </div>

            </div>

        </div>

        <!-- TAB ABSENCES -->
        <div class="tab-pane fade h-100"
             id="absence-tab-pane"
             role="tabpanel">

            <div class="d-flex flex-column h-100" style="min-height:0">

                <?php require __DIR__ . "/Component/filters/filterAbsence.php"; ?>

                <div id="absenceContainer"
                     class="flex-fill overflow-y-auto"
                     role="status"
                     style="min-height:0">
                    Chargement de données...
                </div>

            </div>

        </div>

    </div>

<?php require __DIR__ . "/../ViewOLD/Composants/Modal/modalRule.php"; ?>


<script>
    // Fonctions utilitaires pour l'affichages ect...
    function isMobile() {
        return window.innerWidth < 768;
    }

    function formatDate(dateString) {
        const d = new Date(dateString);

        return d.toLocaleDateString("fr-FR");
    }

    function formatTime(dateString) {
        const d = new Date(dateString);

        const hours = String(d.getHours()).padStart(2, "0");
        const minutes = String(d.getMinutes()).padStart(2, "0");

        return `${hours}h${minutes}`;
    }

    function stateToBadge(state) {
        switch(state) {
            case "Processed":
            case "Validated":
                return "success";

            case "Refused":
                return "danger";

            default:
                return "secondary";
        }
    }

    function formatDateTime(dateString) {
        const d = new Date(dateString);

        return d.toLocaleString("fr-FR");
    }

    function formatDuration(duration) {
        const [h,m] = duration.split(":");

        return parseInt(h) + "h" + m;
    }

    function translateLabelState(state) {
        switch(state) {
            case "Validated":
                return "Validée";
            case "Refused":
                return "Refusée";
            case "NotJustified":
                return "Non justifiée";
            case "Pending":
                return "En attente";
            case "Processed":
                return "Traité";
            case "NotProcessed":
                return "En cours de traitement";
            default:
                return state;
        }
    }

    // Architecture du système Ajax

    // Initialization de valeurs de cache
    let absencesData = []
    let justificationsData = []

    // API
    // Fonctions permettantes d'aller chercher les données avec l'api
    async function fetchAbsences(query = "") {
        const url = query ? "api/absences?" + query : "api/absences";
        const response = await fetch(url);

        if (!response.ok) {
            throw new Error(response.statusText);
        }

        const json = await response.json();
        return json.data || [];
    }

    async function fetchJustifications(query = "") {
        const url = query ? "api/justifications?" + query : "api/justifications";
        const response = await fetch(url);

        if (!response.ok) {
            throw new Error(response.statusText);
        }

        const json = await response.json();
        return json.data || [];
    }

    // "Controller"
    async function loadAbsences(query = "") {
        showLoader("absenceContainer");

        try {
            absencesData = await fetchAbsences(query);
            renderAbsences();
        } catch (error) {
            console.log(error);

            document.getElementById("absenceContainer").innerHTML = `
            <div class="text-center p-3">
                Erreur lors du chargement des absences
            </div>
            `;
        }
    }

    async function loadJustifications(query = "") {
        showLoader("justificationContainer")

        try {
            justificationsData = await fetchJustifications(query);
            renderJustifications();
        } catch (error) {
            console.log(error);

            document.getElementById("justificationContainer").innerHTML = `
            <div class="text-center p-3">
                Erreur lors du chargement des justifications
            </div>
            `;
        }
    }

    // Randerer

    function showLoader(containerId) {
        const container = document.getElementById(containerId);

        container.innerHTML = `
        <div class="d-flex justify-content-center align-items-center h-100">
            <div class="spinner-border"
                 style="width: 3.5rem; height: 3.5rem;"
                 role="status">
                <span class="visually-hidden">Chargement...</span>
            </div>
        </div>
    `;
    }

    function renderAbsences() {
        const container = document.getElementById("absenceContainer");

        if (!absencesData || absencesData.length === 0) {
            container.innerHTML = `
            <div class="text-center p-3">
                Aucune absence
            </div>`;
            return;
        }

        if (isMobile()) {
            renderAbsencesMobile(container, absencesData);
        } else {
            renderAbsencesDesktop(container, absencesData);
        }
    }

    // Affichage des absences sur desktop
    function renderAbsencesDesktop(container, absences) {

        let html = `
    <table id="absenceTable" class="table table-hover align-middle">
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
                            ${abs.teacherFirstName ? abs.teacherFirstName + " " + abs.teacherLastName : "Non renseigné"}
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

        const tooltipTriggerList = container.querySelectorAll('[data-bs-toggle="tooltip"]');

        tooltipTriggerList.forEach(el => {
            new bootstrap.Tooltip(el);
        });
    }

    function renderAbsencesMobile(container, absences) {

        let html = `<div class="d-flex flex-column gap-3 px-2">`;

        absences.forEach(abs => {

            let lockIcon = "";

            if (!abs.allowedJustification && abs.currentState === "Refused") {
                lockIcon = `
                <i class="bi bi-file-lock2 ms-2"
                   data-bs-toggle="tooltip"
                   data-bs-title="Le responsable pédagogique n'autorise pas la justification">
                </i>`;
            }

            html += `
        <div class="card shadow-sm">

            <div class="card-body p-3">

                <div class="d-flex justify-content-between mb-2">
                    <p>Le <strong>${formatDate(abs.time)}</strong> à <strong>${formatTime(abs.time)}</strong> </p>
                </div>

                <div class="mb-2">
                    <strong>Durée :</strong> ${formatDuration(abs.duration)}
                </div>

                <div class="mb-2">
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
                        ${abs.teacherFirstName ? abs.teacherFirstName + " " + abs.teacherLastName : "Non renseigné"}
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

        const tooltipTriggerList = container.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltipTriggerList.forEach(el => new bootstrap.Tooltip(el));
    }


    function renderJustifications() {
        const container = document.getElementById("justificationContainer");

        if (!justificationsData || !justificationsData.length === 0) {
            container.innerHTML = `
            <div class="text-center p-3">
                Aucun justificatif
            </div>`;
            return;
        }

        if (isMobile()) {
            renderJustificationsMobile(container, justificationsData, true);
        } else {
            renderJustificationsDesktop(container, justificationsData, true);
        }
    }

    function renderJustificationsDesktop(container, justifications, showStudent = false) {
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

        justifications.forEach(j => {

            html += `
            <tr>

                ${
                showStudent
                    ? `<td class="text-nowrap">
                        ${j.student.firstName} ${j.student.lastName}
                       </td>`
                    : ""
            }

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

    function renderJustificationsMobile(container, justifications, showStudent = false) {
        let html = `<div class="d-flex flex-column gap-3 px-2">`;

        justifications.forEach(j => {

            html += `
        <div class="card shadow-sm">

            <div class="card-body p-3">

                ${
                showStudent
                    ? `
                    <div class="mb-2">
                        <strong>Étudiant :</strong>
                        ${j.student.firstName} ${j.student.lastName}
                    </div>
                    `
                    : ""
            }

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


    // Events
    // Fonction pour cette page qui permet de charger les données dans le format adapté
    document.addEventListener("DOMContentLoaded", () => {
        loadJustifications();
        loadAbsences();
    });

    // Si la fenêtre change de taille on refait l'affichage sans refaire une requête Ajax
    let resizeTimer;

    window.addEventListener("resize", () => {

        clearTimeout(resizeTimer);

        resizeTimer = setTimeout(() => {
            renderAbsences();
            renderJustifications();
        }, 150);

    });

    // Application des filtres pour les absences
    function applyAbsenceFilters() {
        const dateStart = document.getElementById("absDateStart").value;
        const dateEnd = document.getElementById("absDateEnd").value;
        const exam = document.getElementById("absExam").checked;
        const locked = document.getElementById("absLocked")?.checked;

        const params = new URLSearchParams();

        if (dateStart) params.append("dateStart", dateStart);
        if (dateEnd) params.append("dateEnd", dateEnd);
        if (exam) params.append("examen", true);
        if (locked) params.append("lock", true);

        loadAbsences(params.toString());
    }

    // Application des filtres pour les justificatifs
    function applyJustificationFilters() {
        const dateStart = document.getElementById("proofDateStart").value;
        const dateEnd = document.getElementById("proofDateEnd").value;
        const state = document.getElementById("proofState")?.value;

        const params = new URLSearchParams();

        if (dateStart) params.append("filters[dateStart]", dateStart);
        if (dateEnd) params.append("filters[dateEnd]", dateEnd);
        if (state) params.append("filters[currentState]", state);

        loadJustifications(params.toString());
    }


    document.querySelectorAll(".apply-filters").forEach(btn => {
        btn.addEventListener("click", () => {
            const target = btn.dataset.target;

            if (target === "absence") {
                applyAbsenceFilters();
            }
            if (target === "justification") {
                applyJustificationFilters();
            }

            // fermeture de la fenêtre quand nous sommes sur mobile
            if (isMobile()) {
                const collapse = btn.closest(".collapse");
                if (collapse) {
                    bootstrap.Collapse.getInstance(collapse)?.hide();
                }
            }

        });

    });
</script>