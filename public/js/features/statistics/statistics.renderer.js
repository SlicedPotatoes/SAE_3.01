export function buildChart(domElement, data, title = '') {
    return new Chart(domElement, {
        type: 'pie',
        data: {
            labels: data['labels'],
            datasets: [{
                data: data['data'],
                backgroundColor: data['backgroundColor']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            animation: false,
            plugins: {
                title: {
                    display: true,
                    text: title
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            // récupération des éléments depuis le context
                            const dataset = context.dataset;
                            const chart = context.chart;

                            // Calcul du nombre total d'élément pour les données actuellement visible
                            const visibleData = dataset.data.filter((value, index) => chart.getDataVisibility(index));
                            const total = visibleData.reduce((a, b) => a + b, 0);

                            // Calcul du %age pour la donnée actuelle
                            const value = dataset.data[context.dataIndex];
                            const percentage = ((value / total) * 100).toFixed(2);

                            // String de l'affichage
                            return `${context.label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}