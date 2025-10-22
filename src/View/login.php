<?php
/*
 * Front de la page de connexion
 */

// Rediriger vers le dashboard, si l'utilisateur est connecté
global $role;
if($role != null) {
    header("Location: index.php?currPage=dashboard");
}

require_once './Model/Account.php';
?>

<div class="d-flex gap-2">
    <?php

    // Temporaire, compte hardcodé
    $datas = Account::getAllAccount();

    foreach($datas as $user) {
        $color = "btn-primary";
        if($user->getAccountType() == AccountType::Teacher) {
            $color = "btn-warning";
        }
        if($user->getAccountType() == AccountType::EducationalManager) {
            $color = "btn-danger";
        }
        if($user->getAccountType() == AccountType::Secretary) {
            $color = "btn-secondary";
        }

        echo "<form action='./Presentation/login.php' method='POST'>";
        echo "<input type='hidden' name='id' value='".$user->getIdAccount()."'>";
        echo "<button class='btn $color' type='submit'>".$user->getFirstName()." ".$user->getLastName()."</button>";
        echo "</form>";
    }

    ?>

</div>

