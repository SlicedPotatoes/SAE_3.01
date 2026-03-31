<?php
/**
 * Page du "dashboard" permettant au Responsable Pédagogique de pouvoir voir les justificatifs qu'il reste à traité et les justificatifs qui ont déjà était traité
 */

use Uphf\GestionAbsence\Utils\Renderer;

Renderer::pushAsset('script', '<script type="module" src="/js/pages/justificationDashboard.js"></script>');
?>

<div class="card-md p-md-3 scroll-parent">

    <!-- Tab bar -->
    <ul class="nav nav-tabs d-none d-md-flex"
        id="tab-dashboard-stu"
        role="tablist">

        <li class="nav-item"
            role="presentation">
            <button
                class="text-black nav-link active"
                id="proofToDo-tab"
                data-bs-toggle="tab"
                data-bs-target="#proofToDo-tab-pane"
                type="button"
                role="tab">
                À traiter
            </button>
        </li>

        <li class="nav-item"
            role="presentation">
            <button
                class="text-black nav-link"
                id="proofDone-tab"
                data-bs-toggle="tab"
                data-bs-target="#proofDone-tab-pane"
                type="button"
                role="tab">
                Traités
            </button>
        </li>
    </ul>

    <div class="tab-content border-bottom border-start border-end no-border-sm rounded-bottom pt-3 bg-white scroll-parent"
         id="tab-dashboard-proofContent">

        <!-- TAB JUSTIFICATIFS A TRAITER -->
        <div class="tab-pane fade show active h-100"
             id="proofToDo-tab-pane"
             role="tabpanel">

            <div class="scroll-parent h-100">

                <?php
                $prefix = "todo";
                require __DIR__ . "/Component/Filters/filterJustification.php";
                ?>

                <div id="justificationContainer"
                     class="scrollable"
                     role="status">
                    Chargement de données...
                </div>

            </div>

        </div>

        <!-- TAB ABSENCES -->
        <div class="tab-pane fade h-100"
             id="proofDone-tab-pane"
             role="tabpanel">

            <div class="scroll-parent h-100">

                <?php
                $prefix = "done";
                require __DIR__ . "/Component/Filters/filterJustification.php";
                ?>

                <div id="proofDoneContainer"
                     class="scrollable"
                     role="status">
                    Chargement de données...
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mobile-bottom-nav d-md-none" role="tablist">

    <button class="btn-nav active"
            id="proofToDo-tab-mobile"
            data-bs-toggle="tab"
            data-bs-target="#proofToDo-tab-pane"
            type="button"
            role="tab">
        <i class="bi bi-clipboard-minus-fill fs-3"></i>
        <span>À traiter</span>
    </button>

    <button class="btn-nav"
            id="proofDone-tab-mobile"
            data-bs-toggle="tab"
            data-bs-target="#proofDone-tab-pane"
            type="button"
            role="tab">
        <i class="bi bi-clipboard-check-fill fs-3"></i>
        <span>Traités</span>
    </button>
</div>