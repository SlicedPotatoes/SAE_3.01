<!--Récupération des données-->
<?php
$lastName = $_SESSION['account']->getLastName();
$firstName = $_SESSION['account']->getFirstName();
?>

<!-- En tête saluant l'étudiant -->
<h1 class="h3">Bonjour <span class="text-uphf fw-bold"><?=$firstName, " ", $lastName?></span> !</h1>
<div class="header-line-brand-color"></div>