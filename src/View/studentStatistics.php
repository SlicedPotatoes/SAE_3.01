<?php
/**
 * Front end de la page sur les statistiques sur un étudiant
 */

use Uphf\GestionAbsence\Model\Statistics\Chart;

global $dataView;

require __DIR__ . "/Composants/header.php";
?>

<script src="/script/chart.js"></script>
<div class="card p-3 flex-fill d-flex flex-column" style="min-height: 0">
    <?php
        $type = 'bar';
        $data = [
            "labels" => ["Red", "Blue", "Yellow", "Green", "Purple", "Orange"],
            "datasets" => [
                [
                    "labels" => '# of Votes',
                    "data" => [12, 19, 3, 5, 2, 3],
                    "borderWidth" => 1
                ]
            ]
        ];
        $options = [
            "scales" => [
                "y" => [
                    "beginAtZero" => true
                ]
            ]
        ];

        echo new Chart($type, $data, $options)->toHtml();
    ?>
</div>