<?php
    if($role != null) {
        header("Location: index.php?currPage=dashboard");
    }
?>

<div class="d-flex gap-2">
    <?php
    $datas = [
        -1 => "Dimitri V.",
        -2 => "Isaac G.",
        -3 => "Esteban H.",
        -4 => "Yann D.",
        -5 => "Kevin M.",
        -6 => "Louis P."
    ];

    foreach($datas as $id => $value) {
        echo "<form action='./Presentation/login.php' method='POST'>";
        echo "<input type='hidden' name='id' value='$id'>";
        echo "<button class='btn btn-primary' type='submit'>$value</button>";
        echo "</form>";
    }

    ?>

</div>

