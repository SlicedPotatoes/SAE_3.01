// Récupération des éléments dans le DOM
const tabs = document.querySelectorAll('.tab-draggable');
const dragzone = document.getElementById('drag-zone');
const leftzone = document.getElementById('left-zone');

/**
 * Ajout des événements sur les tabs
 */
tabs.forEach(tab => {
    // Fait apparaitre la zone de drop quand on drag un tab
    tab.addEventListener('dragstart', (e) => {
        e.dataTransfer.setData('text/plain', tab.getAttribute('data-bs-target'));
        dragzone.style.border = '3px dashed var(--color-uphf)!important';
    });

    // Fait disparaitre la zone de drop quand on lache la tab
    tab.addEventListener('dragend', (e) => {
        dragzone.style.border = 'none';
    })
});

// Autoriser le drop sur la zone de drop
dragzone.addEventListener('dragover', (e) => e.preventDefault());

// Ajout d'un événement au drop sur la zone de drop
dragzone.addEventListener('drop', (e) => {
    e.preventDefault();

    // Récupérer les éléments du DOM
    const id = e.dataTransfer.getData('text/plain').slice(1);
    const div = document.getElementById(id)
    const canvas = div.querySelector('canvas');

    // Récupérer l'ancien canvas s'il y en avais un pour le supprimer
    const prevCanvas = dragzone.querySelector('canvas');
    if(prevCanvas) {
        prevCanvas.remove();
    }

    // Création d'une copy du chart qui ce trouvé dans l'onglet
    const copy = document.createElement('canvas');
    new Chart(copy, chartJsDatas[canvas.id]);

    // Modifier la disposition de la page
    leftzone.classList.remove('col-11')
    leftzone.classList.add('col-6')
    dragzone.classList.remove('col-1')
    dragzone.classList.add('col-6')

    // Ajouter la copy dans la zone de drag
    dragzone.querySelector('div').appendChild(copy)
});