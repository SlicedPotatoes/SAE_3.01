<?php

namespace Uphf\GestionAbsence\Model\Statistics;

use JsonException;

/**
 * Wrapper PHP pour ChartJS
 *
 * Version simplifier de https://github.com/bbsnly/chartjs-php (Celle ci avais un probleme sur la dernière version de PHP)
 */
class Chart {
    private string $type;
    private array $data;
    private array $options;

    public function __construct(string $type, array $data, array $options) {
        $this->type = $type;
        $this->options = $options;
        $this->data = $data;
    }

    /**
     * Renvoie les informations du graphique au format JSON
     *
     * @return string
     * @throws JsonException
     */
    private function toJson(): string {
        $arr = [
            'type' => $this->type,
            'data' => $this->data,
            'options' => $this->options
        ];

        return json_encode($arr, JSON_THROW_ON_ERROR | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
    }

    /**
     * Renvoie un string qui doit être mis dans le HTML d'une page pour afficher le graphique
     *
     * @return string
     * @throws JsonException
     */
    public function toHtml(): string {
        $elementId = htmlspecialchars('chart_' . uniqid(), ENT_QUOTES, 'UTF-8');

        return
            '<canvas id="' . $elementId . '"></canvas>
            <script>
                let ctx = document.getElementById("' . $elementId . '");
                
                let chartData = ' . $this->toJson() . ';
                
                // Si la key existe, "transforme" le string en fonction (pointer vers l\'adresse memoire de la fonction)
                if(chartData.options?.plugins?.tooltip?.callbacks.label) {
                    let fName = chartData.options.plugins.tooltip.callbacks.label;
                    chartData.options.plugins.tooltip.callbacks.label = window[fName];
                }
                
                if(!ctx) {
                    console.error("Canvas element not found: ' . $elementId . '");
                }
                else {
                    new Chart(ctx, chartData);
                }
                
            </script>';
    }

    /**
     * Renvoie les options pour un Pie Chart classique
     *
     * @return array
     */
    public static function getOptionsForPieChart(): array {
        return [
            "scales" => [
                "y" => [
                    "beginAtZero" => true
                ]
            ],
            "plugins" => [
                "tooltip" => [
                    "callbacks" => [
                        "label" => "tooltipWithTotalAndProportion"
                    ]
                ]
            ]
        ];
    }
}