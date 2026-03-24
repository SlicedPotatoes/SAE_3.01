const notificationsContainer = document.getElementById("notificationsContainer");
const maxVisible = 2;

let queue = [];
let visibleAlerts = [];

/**
 * Permet d'afficher les notifications
 */
function render() {
    notificationsContainer.style.display = 'block';

    while(visibleAlerts.length < maxVisible && queue.length > 0) {
        const next = queue.shift();
        next.classList.add('showing');
        requestAnimationFrame(() => {
            next.classList.add('show');
        })

        visibleAlerts.push(next);
        notificationsContainer.appendChild(next);
    }

    if(visibleAlerts.length === 0) {
        notificationsContainer.style.display = 'none';
    }
}

/**
 * Appelé quand une notification est supprimé
 * @param element
 */
function closeAlert(element) {
    element.remove();
    visibleAlerts = visibleAlerts.filter(a => a !== element);
    render();
}

// Traité les notifications envoyé via PHP
notificationsContainer.querySelectorAll('.alert').forEach(alert => {
   if(visibleAlerts.length < maxVisible) {
       alert.classList.add('showing');
       alert.classList.add('show');
       visibleAlerts.push(alert);
   }
   else {
       alert.remove();
       queue.push(alert);
   }

   alert.querySelector('.btn-close').addEventListener('click', () => {
       closeAlert(alert);
   })
});
render();

/**
 * Récupérer la couleur pour un type de notification
 * @param typeNotification
 * @returns {string}
 */
function getColor(typeNotification) {
    switch (typeNotification) {
        case 'Error': return 'danger';
        case 'Warning': return 'warning';
        case 'Success': return 'success';
        default: return '';
    }
}

/**
 * Récupérer l'icône pour un type de notification
 * @param typeNotification
 * @returns {string}
 */
function getIcon(typeNotification) {
    switch (typeNotification) {
        case 'Error':
        case 'Warning':
            return 'bi-exclamation-triangle-fill';
        case 'Success':
            return 'bi-check-circle-fill';
        default: return '';
    }
}

/**
 * Ajouter une notification
 *
 * @param typeNotification le type de la notification parmis "Error", "Warning" et "Success"
 * @param message message de la notification
 */
export function addNotification(typeNotification, message) {
    const alertContainer = document.createElement('div');
    alertContainer.classList.add('alert', 'd-flex', 'align-items-center', 'alert-dismissible', `alert-${getColor(typeNotification)}`);
    alertContainer.role = 'alert';

    const icon = document.createElement('i');
    icon.classList.add('bi', 'flex-shrink-0', 'me-2', getIcon(typeNotification), `text-${getColor(typeNotification)}`);

    const messageContainer = document.createElement('div');
    messageContainer.classList.add(`text-${getColor(typeNotification)}`);
    messageContainer.innerText = message;

    const button = document.createElement('button');
    button.classList.add('btn-close');
    button.dataset.bsDismiss = "alert";
    button.ariaLabel = "Close";
    button.addEventListener('click', () => {
        closeAlert(alertContainer);
    })

    alertContainer.append(icon, messageContainer, button);

    queue.push(alertContainer);
    render();
}