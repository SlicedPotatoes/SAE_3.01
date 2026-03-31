<?php
/**
 * View de detail-justification
 *
 * - Définis les scripts / style à injecter dans le mainLayout
 * - Affiche les différentes slides
 */

use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
use Uphf\GestionAbsence\Model\Entity\Justification\StateJustif;
use Uphf\GestionAbsence\Utils\Renderer;

$justification = $data['justification'];
$isEducationManager = AuthManager::isRole(AccountType::EducationalManager);
$isProcessed = $justification->getCurrentState() === StateJustif::Processed;

$title = 'Justificatif';
if($isEducationManager) {
    $title .= ' de ' . $justification->getStudent()->getFirstName() . ' ' . $justification->getStudent()->getLastName() . ',';
}
$title .= ' du ' . $justification->getSendDate()->format('d/m/Y');

$nbSlides = $isEducationManager && !$isProcessed ? 3 : 2;

Renderer::pushAsset('head', '<link rel="stylesheet" href="/style/toggle.css">');
Renderer::pushAsset('head', '<link rel="stylesheet" href="/style/validation-form.css">');
Renderer::pushAsset('head', '<link rel="stylesheet" href="/style/filePreviewModal.css"');

Renderer::pushAsset('script', '<script type="module" src="/js/pages/detail-justification/main.js"></script>');
Renderer::pushAsset('script', '<script type="module" src="/js/pages/detail-justification/file-preview-modal.js"></script>');

require_once __DIR__ . "/Component/Modal/filePreviewModal.php";
?>

<div class="card scroll-parent p-3">
    <!-- Header de la card -->
    <div class="d-flex align-items-center flex-wrap gap-3 mb-3">
        <h3 class="mb-0"><?= $title ?></h3>
        <span class="badge rounded-pill text-bg-<?= $justification->getCurrentState()->colorBadge() ?> fs-6 px-3 py-2"><?= $justification->getCurrentState()->label() ?></span>
        <?php if($isEducationManager): ?>
            <a href="/profil-etudiant/<?= $justification->getStudent()->getIdAccount() ?>" class="btn btn-outline-uphf"><i class="bi bi-person-fill"></i> Profil de l'étudiant</a>
        <?php endif; ?>
    </div>

    <?php
    require_once __DIR__ . '/Component/Pages/detailJustification/slide1.php';
    require_once __DIR__ . '/Component/Pages/detailJustification/slide2.php';
    if ($isEducationManager): require_once __DIR__ . '/Component/Pages/detailJustification/slide3.php'; endif;
    ?>
</div>