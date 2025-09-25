<form class="border-bottom px-4" action="" method="GET">
    <div class="d-flex flex-row gap-3 pb-4">
        <div class="row g-3 align-items-center">
            <div class="col-auto">
                <label for="startDate" class="col-form-label">Date début:</label>
            </div>
            <div class="col-auto">
                <input type="date" id="startDate" class="form-control">
            </div>
        </div>

        <div class="row g-3 align-items-center">
            <div class="col-auto">
                <label for="endDate" class="col-form-label">Date fin:</label>
            </div>
            <div class="col-auto">
                <input type="date" id="endDate" class="form-control">
            </div>
        </div>

        <div class="row g-3 align-items-center">
            <div class="col-auto">
                <label for="state" class="col-form-label">Etat:</label>
            </div>
            <div class="col-auto">
                <select class="form-select" id="state">
                    <?php
                        foreach($states as $value) {
                            echo "<option value='".$value->getId()."'>".$value->getLabel()."</option>";
                        }
                    ?>
                </select>
            </div>
        </div>

        <div class="row g-3 align-items-center">
            <div class="col-auto">
                <input class="form-check-input" type="checkbox" value="" id="exam">
            </div>
            <div class="col-auto">
                <label for="exam" class="form-check-label">Examen</label>
            </div>
        </div>

        <button class="btn btn-uphf" type="submit">Appliquer</button>
    </div>
</form>