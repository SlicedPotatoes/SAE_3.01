<?php
/**
 * Front de la page de connexion
 */

use Uphf\GestionAbsence\Model\Entity\Account\Account;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
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

        echo "<form method='POST'>";
        echo "<input type='hidden' name='id' value='".$user->getIdAccount()."'>";
        echo "<button class='btn $color' type='submit'>".$user->getFirstName()." ".$user->getLastName()."</button>";
        echo "</form>";
    }

    ?>

</div>

