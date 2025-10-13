<?php
/*
 * Front de la page de connexion
 */

    // Rediriger vers le dashboard, si l'utilisateur est connecté
    if($role != null) {
        header("Location: index.php?currPage=dashboard");
    }
?>

<div class="d-flex gap-2">
    <?php

    // Temporaire, compte hardcodé
    $datas = [
        -1 => "Dimitri van Steenkiste",
        -2 => "Isaac Godisiabois",
        -3 => "Esteban Helin",
        -4 => "Yann Dascotte",
        -5 => "Kevin Masmejean",
        -6 => "Louis Picouleau"
    ];

    foreach($datas as $id => $value) {
        echo "<form action='./Presentation/login.php' method='POST'>";
        echo "<input type='hidden' name='id' value='$id'>";
        echo "<button class='btn btn-primary' type='submit'>$value</button>";
        echo "</form>";
    }

    ?>

</div>

