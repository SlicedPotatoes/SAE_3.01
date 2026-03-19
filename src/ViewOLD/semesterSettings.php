<?php
require_once __DIR__ . "/Composants/header.php";
require_once __DIR__ . "/Composants/Modal/modalEditSemester.php";

$semester1 = $dataView->semester1;
$semester2 = $dataView->semester2;
?>

<div class="card p-4 rounded w-100 mt-4">
    <h4 class="mb-4">Définir les semestres</h4>

    <div class="row row-cols-1 row-cols-md-2 g-3">
        <!-- Carte Semestre 1 -->
        <div class="col">
            <div class="card shadow-sm h-100">
                <div class="card-header text-white" style="background-color: var(--color-uphf)">
                    <h5 class="mb-0"><?= htmlspecialchars($semester1->label) ?></h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-muted small">Date de début</div>
                            <div class="fs-6"><?= htmlspecialchars(date('d/m/Y', strtotime($semester1->startDate))) ?></div>
                        </div>
                        <div class="col">
                            <div class="text-muted small">Date de fin</div>
                            <div class="fs-6"><?= htmlspecialchars(date('d/m/Y', strtotime($semester1->endDate))) ?></div>
                        </div>
                        <div class="col-auto">
                            <button type="button"
                                    class="btn btn-sm btn-outline-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEditSemester"
                                    data-id="<?= htmlspecialchars($semester1->id) ?>"
                                    data-label="<?= htmlspecialchars($semester1->label) ?>"
                                    data-start="<?= htmlspecialchars($semester1->startDate) ?>"
                                    data-end="<?= htmlspecialchars($semester1->endDate) ?>">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Carte Semestre 2 -->
        <div class="col">
            <div class="card shadow-sm h-100">
                <div class="card-header text-white" style="background-color: var(--color-uphf)">
                    <h5 class="mb-0"><?= htmlspecialchars($semester2->label) ?></h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-muted small">Date de début</div>
                            <div class="fs-6"><?= htmlspecialchars(date('d/m/Y', strtotime($semester2->startDate))) ?></div>
                        </div>
                        <div class="col">
                            <div class="text-muted small">Date de fin</div>
                            <div class="fs-6"><?= htmlspecialchars(date('d/m/Y', strtotime($semester2->endDate))) ?></div>
                        </div>
                        <div class="col-auto">
                            <button type="button"
                                    class="btn btn-sm btn-outline-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEditSemester"
                                    data-id="<?= htmlspecialchars($semester2->id) ?>"
                                    data-label="<?= htmlspecialchars($semester2->label) ?>"
                                    data-start="<?= htmlspecialchars($semester2->startDate) ?>"
                                    data-end="<?= htmlspecialchars($semester2->endDate) ?>">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/script/semester.js"></script>
