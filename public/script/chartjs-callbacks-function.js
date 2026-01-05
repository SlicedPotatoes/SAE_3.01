/**
 * Définir les éléments afficher sur le tooltip trigger par le hover sur un élément d'un chart
 * Affiche le nombre total de cet élément et le %age par rapport aux nombres total d'élément visible
 *
 * @param context
 * @returns {string}
 */
function tooltipWithTotalAndProportion(context) {
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