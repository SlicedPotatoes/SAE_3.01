<?php
global$currTabValue, $states, $filter, $currPage;
?>
<form class="border-bottom px-4 m-0" action="index.php" method="GET">
    <input type="hidden" name="currPage" value="<?=$currPage?>">
    <input type="hidden" name="currTab" value="<?= $currTabValue ?>">

    <div class="d-flex flex-row gap-3 pb-4">
        <div class="row g-3 align-items-center">
            <div class="col-auto">
                <label for="<?= $currTabValue ?>DateStart" class="col-form-label">Date début:</label>
            </div>
            <div class="col-auto">
                <input
                        type="date"
                        id="<?= $currTabValue ?>DateStart"
                        class="form-control"
                        name="<?= $currTabValue ?>DateStart"
                        value="<?= $filter[$currTabValue]['DateStart'] ?>"
                >
            </div>
        </div>

        <div class="row g-3 align-items-center">
            <div class="col-auto">
                <label for="<?= $currTabValue ?>DateEnd" class="col-form-label">Date fin:</label>
            </div>
            <div class="col-auto">
                <input
                        type="date"
                        id="<?= $currTabValue ?>DateEnd"
                        class="form-control"
                        name="<?= $currTabValue ?>DateEnd"
                        value="<?= $filter[$currTabValue]['DateEnd'] ?>"
                >
            </div>
        </div>

        <div class="row g-3 align-items-center">
            <div class="col-auto">
                <label for="<?= $currTabValue ?>State" class="col-form-label">Etat:</label>
            </div>
            <div class="col-auto">
                <select
                        class="form-select"
                        id="<?= $currTabValue ?>State"
                        name="<?= $currTabValue ?>State"
                >
                    <option value="" <?= $filter[$currTabValue]['State'] == null ? 'selected' : '' ?>>Tout</option>
                    <?php
                        foreach($states as $state) {
                            echo "<option value='".$state->value."'". ($filter[$currTabValue]['State'] == $state->value ? 'selected' : '') .">".$state->label()."</option>";
                        }
                    ?>
                </select>
            </div>
        </div>

        <div class="row g-3 align-items-center">
            <div class="col-auto">
                <input
                        class="form-check-input"
                        type="checkbox" id="<?= $currTabValue ?>Exam"
                        name="<?= $currTabValue ?>Exam"
                        <?= $filter[$currTabValue]['Exam'] ? 'checked' : '' ?>
                >
            </div>
            <div class="col-auto">
                <label for="<?= $currTabValue ?>Exam" class="form-check-label">Examen</label>
            </div>
        </div>

        <button class="btn btn-uphf" type="submit">Appliquer les filtres</button>
    </div>
</form>