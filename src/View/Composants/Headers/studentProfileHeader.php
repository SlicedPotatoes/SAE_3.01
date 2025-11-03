<?php
/**
 * Header du dashboard / profil étudiant
 */

use Uphf\GestionAbsence\Presentation\StudentPresentation;
use Uphf\GestionAbsence\Model\Account\AccountType;
global $role;

// Récupération des données

$studentAccount = StudentPresentation::getStudentAccountDashboard();

$lastName = $studentAccount->getLastName();
$firstName = $studentAccount->getFirstName();

if ($_SESSION["role"] == AccountType::Student)
{
    $message = "<p class='h3'>Bonjour <span class='text-uphf fw-bold'> $firstName $lastName</span> !</p>";
}
else
{
    $message = "<p class='h3'>Profile de <span class='text-uphf fw-bold'> $firstName $lastName</span>.</p>";
}

?>

<!-- En tête saluant l'étudiant -->
<div class="mt-3 accordion" id="accordionCard">
    <div class="accordion-item border-0 bg-transparent">
        <h2 class="accordion-header">
            <button class="accordion-button bg-transparent shadow-none p-0" type="button" data-bs-toggle="collapse" data-bs-target="#showCard" aria-expanded="true" aria-controls="showCard">
                <?= $message ?>
            </button>
            <div class="header-line-brand-color"></div>
        </h2>

        <div id="showCard" class="accordion-collapse collapse show" data-bs-parent="#accordionCard">
            <div class="accordion-body p-0 mt-3">
                <?php require __DIR__ . "/../cards.php"; ?>
            </div>
        </div>
    </div>
</div>