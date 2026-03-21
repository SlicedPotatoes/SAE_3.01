<form id="addJustificationForm" method="post" action="/api/justifications" enctype="multipart/form-data">

    <div class="mb-3">
        <h4 class="mb-2">Justification d’absence</h4>

        <div class="d-flex flex-column flex-md-row gap-2 small">
            <div>
                <label class="fw-semibold">Début :</label>
                <input name="startDate" type="date" class="form-control form-control-sm" required>
            </div>

            <div>
                <label class="fw-semibold">Fin :</label>
                <input name="endDate" type="date" class="form-control form-control-sm" required>
            </div>
        </div>
    </div>

    <div class="row g-4 flex-fill">

        <div class="col-12 col-md-7">
            <label class="form-label h5">Motif</label>
            <textarea name="absenceReason" class="form-control" rows="8" required></textarea>
        </div>

        <div class="col-12 col-md-5">
            <label class="form-label h5">Fichier</label>
            <input type="file" class="form-control" multiple id="justificationFileInput">

            <ul id="justificationFileList"
                class="list-group mt-2 overflow-auto"
                style="max-height:200px"></ul>
        </div>

    </div>

    <div class="mt-3 d-flex">
        <span class="text-muted me-auto small">
            Vos déclarations doivent être exactes
        </span>

        <button type="submit" class="btn btn-uphf">
            Envoyer
        </button>
    </div>

</form>

<script type="module" src="/js/features/justifications/formJustification.js"></script>