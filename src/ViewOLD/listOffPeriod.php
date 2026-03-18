<?php

$firstPartMessage = 'Planifiez vos';
$secondPartMessage = 'périodes de congés';
$thirdPartMessage = 'en toute simplicité';
$showCards = false;
require_once __DIR__ . "/Composants/header.php";
require_once __DIR__ . '/Composants/Modal/modalAddOffPeriod.php';
?>

<script src="/public/script/holiday.js"></script>

<div class="card p-3 flex-fill d-flex flex-column" style="min-height: 0">

    <div class="d-flex justify-content-between ms-auto mb-3">
        <button type="button" class="btn btn-uphf" data-bs-toggle="modal" data-bs-target="#modalAddOffPeriod">
            Ajouter une période
        </button>
    </div>

    <?php if (empty($data['listHoliday'])): ?>
        <div class="d-flex flex-column align-items-center justify-content-center h-100">
            <p class='fs-1 text-body-secondary p-3'>Aucune période enregistrée.</p>
        </div>
    <?php else: ?>
        <table class="table rounded-top overflow-hidden">
            <thead class="table-uphf">
            <tr>
                <th scope="col">#</th>
                <th scope="col">Libellé</th>
                <th scope="col">Date début</th>
                <th scope="col">Date fin</th>
                <th scope="col">Actions</th>
            </tr>
            </thead>
            <tbody>

            <?php foreach ($data['listHoliday'] as $holiday) : ?>

                <tr>
                    <td><?= htmlspecialchars($holiday->getId(), ENT_QUOTES) ?></td>
                    <td><div class="text-truncate" style="max-width: 150px" title="<?= htmlspecialchars($holiday->getPeriodName(), ENT_QUOTES) ?>"><?= htmlspecialchars($holiday->getPeriodName(), ENT_QUOTES) ?></div></td>
                    <td><?= htmlspecialchars($holiday->getStartDate()->format("d/m/Y"), ENT_QUOTES) ?></td>
                    <td><?= htmlspecialchars($holiday->getEndDate()->format("d/m/Y"), ENT_QUOTES) ?></td>
                    <td>
                        <form id="deleteOff<?= htmlspecialchars($holiday->getId(), ENT_QUOTES) ?>" method="post" class="d-inline" onsubmit="return confirm('Voulez-vous supprimer cette période ?'); ">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($holiday->getId(), ENT_QUOTES) ?>">
                            <input type="hidden" name="action" value="delete">
                        </form>
                        <div class="btn-group btn-group-sm">
                            <button type="button"
                                    class="btn btn-outline-primary bi bi-pencil-square me-1 btn-edit btn-edit-offperiod"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalAddOffPeriod"
                                    data-id="<?= htmlspecialchars($holiday->getId(), ENT_QUOTES) ?>"
                                    data-name="<?= htmlspecialchars($holiday->getPeriodName(), ENT_QUOTES) ?>"
                                    data-start="<?= htmlspecialchars($holiday->getEndDate()->format("d/m/Y"), ENT_QUOTES) ?>"
                                    data-end="<?= htmlspecialchars($holiday->getEndDate()->format("d/m/Y"), ENT_QUOTES) ?>">
                            </button>

                            <button form="deleteOff<?= htmlspecialchars($holiday->getId(), ENT_QUOTES) ?>" type="submit" class="btn btn-outline-danger bi bi-trash"></button>
                        </div>
                    </td>
                </tr>

            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
