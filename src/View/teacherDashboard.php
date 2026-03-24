<?php
/** Front-end pour le tableau de bord professeur */
?>

<div class="card-md p-md-3 flex-fill d-flex flex-column"
     style="min-height:0">

    <!-- Tab bar -->
    <ul class="nav nav-tabs d-none d-md-flex"
        id="tab-dashboard-teacher"
        role="tablist">

        <li class="nav-item"
            role="presentation">
            <button
                class="text-black nav-link active"
                id="timeslot-tab"
                data-bs-toggle="tab"
                data-bs-target="#timeslot-tab-pane"
                type="button"
                role="tab">
                Justificatifs
            </button>
        </li>
    </ul>

    <div class="tab-content border-bottom border-start border-end rounded-bottom pt-3 flex-fill d-flex flex-column bg-white"
         style="min-height:0"
         id="tab-dashboard-teacherContent">

        <!-- TAB TIMESLOT -->
        <div class="tab-pane fade show active h-100"
             id="timeslot-tab-pane"
             role="tabpanel">

            <div class="d-flex flex-column h-100"
                 style="min-height:0">

                <?php
                require __DIR__ . "/Component/filters/filterTimeslot.php";
                ?>

                <div id="timeslotContainer"
                     class="flex-fill overflow-y-auto"
                     role="status"
                     style="min-height:0">
                    Chargement de données...
                </div>

            </div>
        </div>
    </div>
</div>

<script type="module" src="/js/pages/teacherDashboard.js"></script>