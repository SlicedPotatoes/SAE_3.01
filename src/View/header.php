<!-- Message de bienvenu -->
<h1 class="h3">Bonjour <span class="text-uphf fw-bold"><?=$_SESSION['student']->getFirstName(), " ", $_SESSION['student']->getLastName()?></span> !</h1>
<div class="header-line-brand-color"></div>

<!-- Étudiant : Card avec information sur son assiduité -->
<div class="row row-cols-2 row-cols-md-4 g-3 mb-3">

    <div class="col">
        <div class="card shadow-sm border-primary text-center h-100 card-compact">
            <div class="card-body">
                <div class="card-title small mb-1">Absences totales</div>
                <div class="fs-4 text-primary mb-0">
                    <?= (int) $_SESSION['student']->getAbsTotal() ?>
                </div>

                <?php if ($_SESSION['student']->getAbsTotal() > 0): ?>
                    <div class="text-muted small">
                        Demi-journées d’absence totales :
                        <?= (int) $_SESSION['student']->getHalfdaysAbsences() ?>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <div class="col">
        <div class="card shadow-sm border-info text-center h-100 card-compact">
            <div class="card-body">
                <div class="card-title small mb-1">Absences à justifier</div>
                <div class="fs-4 text-info mb-0">
                    <?= (int) $_SESSION['student']->getAbsNotJustified() ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card shadow-sm  border-warning text-center h-100 card-compact">
            <div class="card-body">
                <div class="card-title small mb-1">Absences pénalisantes</div>
                <div class="fs-4 text-warning mb-0">
                    <?= (int) ($_SESSION['student']->getAbsNotJustified() + $_SESSION['student']->getAbsRefused()) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col">
        <?php $malus = (int) $_SESSION['student']->malusPoints(); ?>
        <?php if ($malus > 0): ?>
            <div class="card shadow-sm border-danger text-center h-100 card-compact">
                <div class="card-body">
                    <div class="card-title small mb-1">Malus</div>
                    <div class="fs-4 text-danger mb-0">-<?= $malus ?></div>
                </div>
            </div>
        <?php else: ?>
            <div class="card shadow-sm border-success text-center h-100 card-compact">
                <div class="card-body">
                    <div class="card-title small mb-1">Malus</div>
                    <div class="fs-4 text-success mb-0">Pas de malus</div>
                </div>
            </div>
        <?php endif; ?>
    </div>

</div>