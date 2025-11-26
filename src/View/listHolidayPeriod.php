<?php
$periods = [];
if (isset($dataView) && isset($dataView->periods)) {
    $periods = $dataView->periods;
}

require_once __DIR__ . '/Composants/Modal/modalAddholidayPeriod.php';
?>

<script src="/public/script/holiday.js"></script>

<div class="card p-3 rounded w-100 ">

    <div class="d-flex justify-content-between ms-auto mb-3">
        <button type="button" class="btn btn-uphf" data-bs-toggle="modal" data-bs-target="#modalAddHolidayPeriod">
            Ajouter une période
        </button>
    </div>

    <?php if (empty($periods)): ?>
        <div>
            <p colspan="5" class="text-center">Aucune période enregistrée.</p>
        </div>
    <?php else: ?>
        <table class="table">
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

            <?php foreach ($periods as $period) : ?>

                <tr>
                    <td><?= htmlspecialchars($period->holidaysid, ENT_QUOTES) ?></td>
                    <td><div class="text-truncate" style="max-width: 150px" title="<?= htmlspecialchars($period->HolidayName, ENT_QUOTES) ?>"><?= htmlspecialchars($period->HolidayName, ENT_QUOTES) ?></div></td>
                    <td><?= htmlspecialchars($period->startDate, ENT_QUOTES) ?></td>
                    <td><?= htmlspecialchars($period->endDate, ENT_QUOTES) ?></td>
                    <td>
                        <div class="btn-group btn-group-sm">

                            <form id="deleteHoliday<?= htmlspecialchars($period->holidaysid, ENT_QUOTES) ?>" method="post" class="d-inline" onsubmit="return confirm('Voulez-vous supprimer cette période ?'); ">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($period->holidaysid, ENT_QUOTES) ?>">
                                <input type="hidden" name="action" value="delete">
                            </form>

                            <button type="button"
                                    class="btn btn-outline-primary bi bi-pencil-square me-1 btn-edit btn-edit-holiday"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalAddHolidayPeriod"
                                    data-id="<?= htmlspecialchars($period->holidayid ?? $period->holidaysid, ENT_QUOTES) ?>"
                                    data-name="<?= htmlspecialchars($period->HolidayName, ENT_QUOTES) ?>"
                                    data-start="<?= htmlspecialchars($period->startDate, ENT_QUOTES) ?>"
                                    data-end="<?= htmlspecialchars($period->endDate, ENT_QUOTES) ?>">
                            </button>

                            <button form="deleteHoliday<?= htmlspecialchars($period->holidaysid, ENT_QUOTES) ?>"  type="submit" class="btn btn-outline-danger bi bi-trash"></button>
                        </div>
                    </td>
                </tr>

            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
