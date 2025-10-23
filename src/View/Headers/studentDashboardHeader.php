<?php
/**
 * Header du dashboard / profil étudiant
 */

require_once __DIR__ . "/../../Presentation/StudentPresentation.php";
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
<h1 class="h3"> <?= $message ?></h1>
<div class="header-line-brand-color"></div>

<?php
if ($role == AccountType::Student || $role == AccountType::EducationalManager && $currPage="studentProfile")
{
    require __DIR__ . "/../../View/Dashboard/Student/cards.php";
}
?>