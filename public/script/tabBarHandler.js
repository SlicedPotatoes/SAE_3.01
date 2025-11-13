/**
 * Script pour ajouter une classe personnalisé à la div du contenu d'une tab et enlevé cette même classe à la tab précédente
 * Utiliser dans les vues contenant des tabs bar
 */

const tabsButtonDashboard = document.querySelectorAll('#tab-dashboard-stu button');

tabsButtonDashboard.forEach((tab) => {
    // shown.bs.tab est l'event déclenché lorsqu'une tab deviens active
    tab.addEventListener('shown.bs.tab', (e) => {
        const activatedPane = document.querySelector(tab.getAttribute('data-bs-target'));
        activatedPane.classList.add('d-flex');

        if(e.relatedTarget) {
            const previousPane = document.querySelector(e.relatedTarget.getAttribute('data-bs-target'));
            previousPane.classList.remove('d-flex');
        }
    });
});