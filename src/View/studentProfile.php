<?php
/**
 * Page du "dashboard étudiant", permettant d'observer les absences et justificatifs d'un étudiant
 */

use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\CookieManager;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\Utils\Renderer;

Renderer::pushAsset('script', '<script>const STUDENT_ID = ' . $student->getIdAccount() . ';</script>');
Renderer::pushAsset('script', '<script type="module" src="/js/pages/studentProfile.js"></script>');
?>

<?php if (AuthManager::isRole(AccountType::EducationalManager)) : ?>
    <p class="h3 mb-0 text-center text-md-start">
        Profile de
        <span class='text-uphf fw-bold'> <?= $student->getFirstName() . ' ' . $student->getLastName() ?> </span>
    </p>
<?php endif; ?>

<div class="accordion my-2" id="accordionInformationsCards">
    <div class="accordion-header text-center text-md-start">

        <button class="p-0 border-0 bg-transparent text-decoration-none small"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#studentInformationCards"
                aria-expanded="true"
                aria-controls="studentInformationCards"
        >
            Afficher les indicateurs d'assiduité
        </button>

        <hr class="my-1">

    </div>

    <div id="studentInformationCards"
         class="accordion-collapse collapse <?= CookieManager::getCardOpen() ? 'show' : '' ?>""
         data-bs-parent="#accordionInformationsCards"
    >
        <?php require __DIR__ . "/Component/Pages/studentProfile/studentInformationsCards.php"; ?>
    </div>
</div>

<div class="card-md scroll-parent">

    <!-- Tab bar -->
    <ul class="nav nav-tabs d-none d-md-flex"
        id="tab-dashboard-stu"
        role="tablist">

        <li class="nav-item"
            role="presentation">
            <button
                    class="text-black nav-link active"
                    id="proof-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#proof-tab-pane"
                    type="button"
                    role="tab">
                Justificatifs
            </button>
        </li>

        <li class="nav-item"
            role="presentation">
            <button
                    class="text-black nav-link"
                    id="absence-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#absence-tab-pane"
                    type="button"
                    role="tab">
                Absences
            </button>
        </li>

        <li class="ms-auto">
            <?php if (AuthManager::isRole(AccountType::Student)) : ?>
                <button
                        class="nav-link rounded-bottom-0 btn btn-uphf"
                        id="add-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#add-tab-pane"
                        type="button"
                        role="tab">
                    Déposer un justficatif
                </button>
            <?php endif; ?>
        </li>
    </ul>

    <div class="tab-content border-bottom border-start border-end no-border-sm rounded-bottom pt-3 bg-white scroll-parent"
         id="tab-dashboard-stuContent">

        <!-- TAB JUSTIFICATIFS -->
        <div class="tab-pane fade show active h-100"
             id="proof-tab-pane"
             role="tabpanel">

            <div class="scroll-parent h-100">

                <?php
                $showStateFilter = true;
                require __DIR__ . "/Component/filters/filterJustification.php";
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
             id="absence-tab-pane"
             role="tabpanel">

            <div class="scroll-parent h-100">

                <?php require __DIR__ . "/Component/filters/filterAbsence.php"; ?>

                <div id="absenceContainer"
                     class="scrollable"
                     role="status">
                    Chargement de données...
                </div>
            </div>
        </div>

        <!-- TAB JUSTIFIER UNE ABSENCE -->
        <?php if (AuthManager::isRole(AccountType::Student)) : ?>
            <div class="tab-pane fade h-100"
                 id="add-tab-pane"
                 role="tabpanel">

                <div class="scrollable h-100 p-3">

                    <?php require __DIR__ . "/Component/Pages/studentProfile/formJustification.php"; ?>

                </div>

            </div>
        <?php endif; ?>

    </div>
</div>

<div class="mobile-bottom-nav d-md-none" role="tablist">

    <button class="btn-nav active"
            id="proof-tab-mobile"
            data-bs-toggle="tab"
            data-bs-target="#proof-tab-pane"
            type="button"
            role="tab">
        <i class="bi bi-clipboard-minus-fill fs-3"></i>
        <span>Justificatifs</span>
    </button>

    <button class="btn-nav"
            id="absence-tab-mobile"
            data-bs-toggle="tab"
            data-bs-target="#absence-tab-pane"
            type="button"
            role="tab">
        <i class="bi bi-calendar-week-fill fs-3"></i>
        <span>Absences</span>
    </button>

    <?php if (AuthManager::isRole(AccountType::Student)) : ?>
        <button class="btn-nav"
                id="add-tab-mobile"
                data-bs-toggle="tab"
                data-bs-target="#add-tab-pane"
                type="button"
                role="tab">
            <i class="bi bi-file-earmark-plus-fill fs-3"></i>
            <span>Ajouter</span>
        </button>
    <?php endif; ?>
</div>

<?php
require __DIR__ . "/Component/Modal/modalRule.php";
?>