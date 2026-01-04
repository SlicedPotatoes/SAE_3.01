<?php

namespace Uphf\GestionAbsence\Model;

use JsonException;

/**
 * Wrapper PHP pour ChartJS
 *
 * Version simplifier de https://github.com/bbsnly/chartjs-php (Celle ci avais un probleme)
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
                
                if(!ctx) {
                    console.error("Canvas element not found: ' . $elementId . '");
                }
                else {
                    new Chart(ctx, ' . $this->toJson() . ');
                }
                
            </script>';
    }
}