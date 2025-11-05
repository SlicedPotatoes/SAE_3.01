<?php
/**
 * Front de la barre de filtre, pour les justificatifs et les absences
 */

global $currTabValue, $states, $filter, $currPage;
?>
<form class="border-bottom px-4 m-0" action="index.php" method="GET">
    <!-- Envoi de la page courante et de l'onglet -->
    <input type="hidden" name="currPage" value="<?=$currPage?>">
    <input type="hidden" name="currTab" value="<?= $currTabValue ?>">

    <div class="d-flex flex-row gap-3 pb-3">
        <!-- Input DateStart, type date -->
        <div class="row g-3 align-items-center">
            <div class="col-auto">
                <label for="<?= $currTabValue ?>DateStart" class="col-form-label">Date début:</label>
            </div>
            <div class="col-auto">
                <input
                        type="date"
                        id="<?= $currTabValue ?>DateStart"
                        value="<?= $filter[$currTabValue]->getDateStart() ?>"
                        class="form-control"
                        name="<?= $currTabValue ?>DateStart"
                >
            </div>
        </div>

        <!-- Input DateEnd, type date -->
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
                        value="<?= $filter[$currTabValue]->getDateEnd() ?>"
                >
            </div>
        </div>

        <?php if($currPage != "justificationList") : ?>
        <!-- Select State -->
        <div class="row g-3 align-items-center">
            <div class="col-auto">
                <label for="<?= $currTabValue ?>State" class="col-form-label">État:</label>
            </div>


                <div class="col-auto">
                    <select
                            class="form-select"
                            id="<?= $currTabValue ?>State"
                            name="<?= $currTabValue ?>State"
                    >
                        <option value="" <?= $filter[$currTabValue]->getState() == null ? 'selected' : '' ?>>Tout</option>
                        <?php
                            // Liste des différents états
                            foreach($states as $state) {
                                echo "<option value='".$state->value."'". ($filter[$currTabValue]->getState() == $state->value ? 'selected' : '') .">".$state->label()."</option>";
                            }
                        ?>
                    </select>
                </div>
        </div>
        <?php endif; ?>

        <!-- Input Locked, type date, afficher seulement pour les absences -->
        <?php if($currTabValue == 'abs'): ?>
            <div class="row g-3 align-items-center">
                <div class="col-auto">
                    <input
                            class="form-check-input"
                            type="checkbox" id="<?= $currTabValue ?>Locked"
                            name="<?= $currTabValue ?>Locked"
                            <?= $filter[$currTabValue]->getLocked() ? 'checked' : '' ?>
                    >
                </div>
                <div class="col-auto">
                    <label for="<?= $currTabValue ?>Locked" class="form-check-label">Verrouillé</label>
                </div>
            </div>
        <?php endif; ?>

        <!-- Input Exam, type checkbox -->
        <div class="row g-3 align-items-center flex-grow-1">
            <div class="col-auto">
                <input
                        class="form-check-input"
                        type="checkbox" id="<?= $currTabValue ?>Exam"
                        name="<?= $currTabValue ?>Exam"
                        <?= $filter[$currTabValue]->getExamen() ? 'checked' : '' ?>
                >
            </div>
            <div class="col-auto">
                <label for="<?= $currTabValue ?>Exam" class="form-check-label">Examen</label>
            </div>
        </div>
        <button class="btn btn-uphf ml-auto" type="submit">Appliquer les filtres</button>
    </div>
</form>