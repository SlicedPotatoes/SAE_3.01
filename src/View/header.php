<!-- Message de bienvenu -->
<h1 class="h3">Bonjour <span class="text-uphf fw-bold"><?=$_SESSION['student']->getFirstName(), " ", $_SESSION['student']->getLastName()?></span> !</h1>
<div class="header-line-brand-color"></div>

<!-- Étudiant : Card avec information sur son assiduité -->
<div class="row row-cols-2 row-cols-md-4 g-3" style="margin-bottom: 15px;">
    <div class="col">
        <div class="card shadow-sm border-primary text-center h-100 card-compact">
            <div class="card-body">
                <div class="card-title small mb-1">Absences totales</div>
                <div class="fs-4 text-primary mb-0"><?=$_SESSION['student']->getAbsTotal()?></div>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card shadow-sm border-warning text-center h-100 card-compact">
            <div class="card-body">
                <div class="card-title small mb-1">En attente</div>
                <div class="fs-4 text-warning mb-0"><?=$_SESSION['student']->getAbsPending()?></div>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card shadow-sm border-success text-center h-100 card-compact">
            <div class="card-body">
                <div class="card-title small mb-1">Justifiées</div>
                <div class="fs-4 text-success mb-0"><?=$_SESSION['student']->getAbsValidated()?></div>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card shadow-sm border-danger text-center h-100 card-compact">
            <div class="card-body">
                <div class="card-title small mb-1">Refusées</div>
                <div class="fs-4 text-danger mb-0"><?=$_SESSION['student']->getAbsRefused()?></div>
                <div class="text-muted small">Malus -<?=$_SESSION['student']->malusPoints()?></div>
            </div>
        </div>
    </div>
</div>