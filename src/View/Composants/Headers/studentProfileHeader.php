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
    $message = "Bonjour <span class='text-uphf fw-bold'> $firstName $lastName</span> !";
}
else
{
    $message = "Profile de <span class='text-uphf fw-bold'> $firstName $lastName</span>.";
}

?>

<!-- En tête saluant l'étudiant -->
<div>
    <h1 class="h3"> <?= $message ?></h1>
    <div class="header-line-brand-color"></div>
</div>


<?php
require __DIR__ . "/../cards.php";
?>