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
        e.dataTransfer.setData('text/plain', tab.dataset.drag);
        dragzone.style.border = '2px dashed var(--color-uphf)';
    });

    // Fait disparaitre la zone de drop quand on lache la tab
    tab.addEventListener('dragend', (e) => {
        dragzone.style.border = '2px dashed black';
    })
});

// Autoriser le drop sur la zone de drop
dragzone.addEventListener('dragover', (e) => e.preventDefault());

// Ajout d'un événement au drop sur la zone de drop
dragzone.addEventListener('drop', (e) => {
    e.preventDefault();

    dragzone.innerHTML = '';

    // Création d'une copy du chart qui ce trouvé dans l'onglet
    const key = e.dataTransfer.getData('text/plain');

    const div = document.createElement('div');
    div.classList.add('p-4', 'position-relative', 'h-100', 'w-100');

    const copy = document.createElement('canvas');
    copy.classList.add('position-absolute', 'top-50', 'start-50', 'translate-middle')


    // Récupération du chart à copier
    const chart = Chart.getChart(`${key}-chart`);
    new Chart(copy, chart.config);

    // Modifier la disposition de la page
    leftzone.classList.remove('col-10');
    leftzone.classList.add('col-6');
    dragzone.classList.remove('col-2');
    dragzone.classList.add('col-6');

    // Ajouter la copy dans la zone de drag
    div.appendChild(copy);
    dragzone.appendChild(div);
});