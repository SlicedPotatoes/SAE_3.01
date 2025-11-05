<?php

$account = $_SESSION["account"];

$lastName = $account->getLastName();
$firstName = $account->getFirstName();

$message = "<p class='h3'>Bonjour <span class='text-uphf fw-bold'> $firstName $lastName</span> !</p>";

?>

<!-- En tête saluant l'étudiant -->
<div class="mt-3">
    <?= $message ?>
    <div class="header-line-brand-color"></div>
</div>
