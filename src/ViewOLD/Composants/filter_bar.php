<?php
/**
 * Front de la barre de filtre, pour les justificatifs et les absences
 */

?>
<form class="border-bottom px-4 m-0" method="POST">
    <!-- Envoi de l'onglet -->
    <input type="hidden" name="currTab" value="<?= $tabName ?>">

    <!-- Input DateStart, type date -->
    <div class="d-flex flex-row gap-3 pb-3">
        <div class="row g-3 align-items-center">
            <div class="col-auto">
                <label for="<?= $tabName ?>DateStart" class="col-form-label">Date début:</label>
            </div>
            <div class="col-auto">
                <input
                        type="date"
                        id="<?= $tabName ?>DateStart"
                        class="form-control"
                        name="dateStart"
                >
            </div>
        </div>

        <!-- Input DateEnd, type date -->
        <div class="row g-3 align-items-center">
            <div class="col-auto">
                <label for="<?= $tabName ?>DateEnd" class="col-form-label">Date fin:</label>
            </div>
            <div class="col-auto">
                <input
                        type="date"
                        id="<?= $tabName ?>DateEnd"
                        class="form-control"
                        name="dateEnd"
                >
            </div>
        </div>

        <?php if($data['showState']) : ?>
            <!-- Select State -->
            <div class="row g-3 align-items-center">
                <div class="col-auto">
                    <label for="<?= $tabName ?>State" class="col-form-label">État:</label>
                </div>

                <div class="col-auto">
                    <select
                            class="form-select"
                            id="<?= $tabName ?>State"
                            name="state"
                    >
                        <option value="">Tout</option>
                        <?php
                        // Liste des différents états
                        foreach($states as $state) {
                            echo "<option value='".$state->value."'>".$state->label()."</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
        <?php endif; ?>

        <!-- Input Locked, type checkbox, afficher seulement pour les absences -->
        <?php if($tabName == 'abs'): ?>
            <div class="row g-3 align-items-center">
                <div class="col-auto">
                    <input
                            class="form-check-input"
                            type="checkbox" id="<?= $tabName ?>Locked"
                            name="lock"
                    >
                </div>
                <div class="col-auto">
                    <label for="<?= $tabName ?>Locked" class="form-check-label">Verrouillé</label>
                </div>
            </div>
        <?php endif; ?>

        <!-- Input Exam, type checkbox -->
        <div class="row g-3 align-items-center flex-grow-1">
            <div class="col-auto">
                <input
                        class="form-check-input"
                        type="checkbox" id="<?= $tabName ?>Exam"
                        name="examen"
                >
            </div>
            <div class="col-auto">
                <label for="<?= $tabName ?>Exam" class="form-check-label">Examen</label>
            </div>
        </div>
        <button class="btn btn-uphf ml-auto" type="submit">Appliquer les filtres</button>
    </div>
</form>