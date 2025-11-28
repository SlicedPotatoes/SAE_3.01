/**
 * Script de gestion des alertes
 */

// Récupérer les alertes
const alerts = document.getElementsByClassName('alert');
const notificationsContainer = document.getElementById("notificationsContainer");
const countAlertMaxShow = 3;

// Stocke les alertes non affichées
let alertToDisplay = [];
let countAlert = 0;

// Pour chaque alerte
for(let i = 0; i < alerts.length; i++) {
    const alert = alerts[i];
    const btnClose = alert.getElementsByClassName('btn-close')[0];

    if(!btnClose) { continue; }

    if(i >= countAlertMaxShow) {
        // Ajoute l'alerte aux stack, si i est supérieur au nombre d'alerte que l'on veut afficher
        alertToDisplay.push(alert);
    }
    else {
        alert.classList.add('showing');
        alert.classList.add('show');
    }

    countAlert++;

    // Au click du bouton close de l'alerte
    btnClose.addEventListener('click', () => {
        countAlert--;
        // S'il reste des alertes à afficher
        if(alertToDisplay.length > 0) {
            // Les afficher
            alertToDisplay.at(-1).classList.add('showing')
            requestAnimationFrame(() => {
                alertToDisplay.at(-1).classList.add('show');
                alertToDisplay.pop();
            })
        }

        if(countAlert === 0) {
            notificationsContainer.style.display = "none";
        }
    });
}

// Inverser la liste pour les afficher dans l'ordre
alertToDisplay.reverse();

if(countAlert === 0) {
    notificationsContainer.style.display = "none";
}
