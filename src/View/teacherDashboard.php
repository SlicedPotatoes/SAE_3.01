<?php
/** Front-end pour le tableau de bord professeur */
?>

<div class="card-md p-md-3 scroll-parent">

    <div class="border rounded pt-3 bg-white scroll-parent">
        <div class="scroll-parent h-100">

            <?php
            require __DIR__ . "/Component/Filters/filterTimeslot.php";
            ?>

            <div id="timeslotContainer"
                 class="scrollable"
                 role="status">
                Chargement de données...
            </div>

        </div>
    </div>
</div>

<script type="module" src="/js/pages/teacherDashboard.js"></script>