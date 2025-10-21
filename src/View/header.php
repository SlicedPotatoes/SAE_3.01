<!--Récupération des données-->
<?php
$lastName = $_SESSION['student']->getLastName();
$firstName = $_SESSION['student']->getFirstName();
$absenceTotal = $_SESSION['student']->getAbsTotal();
$halfdayTotal = $_SESSION['student']->getHalfdaysAbsences();
$absenceAllowJustification = $_SESSION['student']->getAbsCanBeJustified();
$absenceNotJustified = $_SESSION['student']->getAbsNotJustified();
$absenceRefused = $_SESSION['student']->getAbsRefused();
$malus = $_SESSION['student']->getMalusPoints();
$malusWithoutPending = $_SESSION['student']->getMalusPointsWithoutPending();
$PenalizingAbsence = $_SESSION['student']->getPenalizingAbsence();
?>

<!-- En tête saluant l'étudiant -->
<h1 class="h3">Bonjour <span class="text-uphf fw-bold"><?=$firstName, " ", $lastName?></span> !</h1>
<div class="header-line-brand-color"></div>

<!-- Card du dashboard avec informations sur l'assiduité -->
<div class="row row-cols-2 row-cols-md-4 g-3 mb-3">

<!--    Card pour afficher les absences totals de l'étudiant-->
    <div class="col">
        <div class="card shadow-sm border-primary text-center h-100 card-compact">
            <div class="card-body">
                <div class="card-title small mb-1">Absences totales</div>
                <div class="fs-4 text-primary mb-0">
                    <?= (int)$absenceTotal ?>
                </div>

<!--                Affichage des demi-journées d'absences si elles sont différentes des absences-->
                <?php if ($absenceTotal ==! $halfdayTotal): ?>
                    <div class="text-muted small">
                        Demi-journées d’absence totales :
                        <?= (int) $halfdayTotal ?>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

<!--    Card pour afficher les absences qui peuvent être justifiée-->
    <div class="col">
        <div class="card shadow-sm border-info text-center h-100 card-compact">
            <div class="card-body">
                <div class="card-title small mb-1">Absences à justifier</div>
                <div class="fs-4 text-info mb-0">
                    <?= (int) $absenceAllowJustification ?>
                </div>
            </div>
        </div>
    </div>

<!--    Absences pénalisante, contribue au malus-->
    <div class="col">
        <div class="card shadow-sm  border-warning text-center h-100 card-compact">
            <div class="card-body">
                <div class="card-title small mb-1">Absences pénalisantes</div>
                <div class="fs-4 text-warning mb-0">
                    <?= (int) $PenalizingAbsence ?>
                </div>
            </div>
        </div>
    </div>

<!--    Malus actuel et projection du malus avec justification des absences en attente -->
    <div class="col">
<!--        If permettant d'avoir deux affichages différents si l'étudiant à un malus ou non-->
<!--        Si l'étudiant à un malus la card sera rouge -->
        <?php if ($malus > 0): ?>
            <div class="card shadow-sm border-danger text-center h-100 card-compact">
                <div class="card-body">
                    <div class="card-title small mb-1">Malus</div>
                    <div class="fs-4 text-danger mb-0">-<?= $malus ?>&nbsp;</div>
<!--                Si l'étudiant a des absences en attente alors le malus pourrais être réduit ou même retiré
                    Ainsi il est important de montrer à l'étudiant l'impacte de la justification de ses absences
                    sur le malus-->
                <?php if ($malusWithoutPending ==! $malus): ?>
                    <div class="text-muted small">
                        Malus si validation des absences : -
                        <?= $malusWithoutPending ?>
                    </div>
                <?php endif; ?>
                </div>
            </div>

<!--        L'étudiant sans malus a ces informations -->
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