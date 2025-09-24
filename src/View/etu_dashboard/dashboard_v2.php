<?php
    require_once "../../Model/State.php";
    require_once "../../Model/Absence.php";
?>

<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Dashboard étudiant</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link rel="stylesheet" href="../Style/style.css">
    </head>
    <body class="bg-light" style="padding: 25px">
        <ul class="nav nav-tabs" id="tab-dashboard-stu" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="proof-tab" data-bs-toggle="tab" data-bs-target="#proof-tab-pane" type="button" role="tab" aria-controls="proof-tab-pane" aria-selected="true">Justificatifs</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="absence-tab" data-bs-toggle="tab" data-bs-target="#absence-tab-pane" type="button" role="tab" aria-controls="absence-tab-pane" aria-selected="false">Absences</button>
            </li>
        </ul>
        <div class="tab-content bg-white border-bottom border-start border-end rounded-bottom py-4" id="tab-dashboard-stuContent">
            <div class="tab-pane fade show active" id="proof-tab-pane" role="tabpanel" aria-labelledby="proof-tab" tabindex="0">
                <?php
                    $states = State::getJustificationStates();
                    require "filter_bar.php";
                ?>
            </div>
            <div class="tab-pane fade show" id="absence-tab-pane" role="tabpanel" aria-labelledby="absence-tab" tabindex="0">
                <?php
                    $states = State::getAbsenceStates();
                    require "filter_bar.php";

                    $listAbs = Absence::getAbsences();

                    foreach($listAbs as $abs) {
                        require "lineAbs.php";
                    }
                ?>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>