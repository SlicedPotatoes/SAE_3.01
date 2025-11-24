<?php
/**
 * Front de la page de connexion
 */

use Uphf\GestionAbsence\Model\Entity\Account\Account;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;
?>

<!--<div class="d-flex gap-2">-->
    <?php
/**
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
*/
    ?>
<!--</div>-->

<div class="border p-4 rounded shadow-sm w-50 mx-auto my-auto"  ">
    <form name="login" method="post" >
        <div class="mb-3">
            <label for="id" class="form-label">Identifiant</label>
            <input type="text" class="form-control" id="id" name="id" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Mot de passe</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>

        <div class="mb-2">
            <button class="btn btn-link float-start" type="button">Mot de passe oublié </button>
        </div>

       <button type="submit" class="btn btn-primary float-end" >Se connecter</button>

    </form>
</div>

