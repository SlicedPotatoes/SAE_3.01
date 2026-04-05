/**
 * Render la liste des étudiants dans le container donné.
 */
export function renderStudents(container, students) {

    if (!students || students.length === 0) {
        container.innerHTML = `
            <div class="d-flex flex-column align-items-center justify-content-center h-100">
                <p class="fs-1 text-body-secondary p-3">Pas d'étudiant</p>
            </div>`;
        return;
    }

    container.innerHTML = students.map(student => {
        const firstName  = escapeHtml(student.firstName          ?? "");
        const lastName   = escapeHtml(student.lastName           ?? "");
        const group      = escapeHtml(student.groupStudent?.label ?? "");
        const studentNb  = escapeHtml(String(student.studentNumber ?? ""));
        const id         = student.idAccount;

        return `
        <div class="card mt-2 bg-transparent">
            <div class="card-body">

                <div class="d-flex align-items-center">
                    <i class="bi bi-person-circle icon-uphf me-3 fs-2 flex-shrink-0"></i>
                    <div class="flex-fill">
                        <h5 class="card-title mb-0">${firstName} ${lastName}</h5>
                        <p class="card-text mb-0">${group}</p>
                        <p class="card-text mb-0">Numéro étudiant: ${studentNb}</p>
                    </div>
                    <!-- Boutons desktop -->
                    <div class="d-none d-md-flex gap-2 ms-3 flex-shrink-0">
                        <a href="/statistiques-etudiant/${id}" class="btn btn-success btn-no-border">Voir les statistiques</a>
                        <a href="/StudentProfile/${id}" class="btn btn-uphf">Voir le profil</a>
                    </div>
                </div>

                <!-- Boutons mobile -->
                <div class="d-flex d-md-none gap-2 mt-2">
                    <a href="/statistiques-etudiant/${id}" class="btn btn-success btn-no-border flex-fill">Voir les statistiques</a>
                    <a href="/StudentProfile/${id}" class="btn btn-uphf flex-fill">Voir le profil</a>
                </div>

            </div>
        </div>`;
    }).join("");
}

function escapeHtml(str) {
    const div = document.createElement("div");
    div.textContent = str;
    return div.innerHTML;
}
