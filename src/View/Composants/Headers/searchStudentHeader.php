<?php

$account = $_SESSION["account"];

$lastName = $account->getLastName();
$firstName = $account->getFirstName();

$message = "<p class='h3'>Vous pouvez rechercher ici <span class='text-uphf fw-bold'>étudiant</span> !</p>";

?>

<!-- En tête saluant l'étudiant -->
<div class="mt-3">
    <?= $message ?>
    <div class="header-line-brand-color"></div>
</div>
