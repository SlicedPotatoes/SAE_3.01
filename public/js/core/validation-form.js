/**
 * Permet l'ajout d'un indicateur visuel sur le label des champs requis
 * @param form
 */
export function initRequiredIndicator(form) {
    Array.from(form.elements).forEach(field => {
        initRequiredIndicatorField(field);
    })
}

/**
 * Permet l'ajout d'un indicateur visuel sur le label d'un champ requis spécifique.
 * @param field
 */
export function initRequiredIndicatorField(field) {
    const label = document.querySelector(`label[for="${field.id}"]`);

    if(label) {
        if(!field.required) {
            label.classList.remove('required');
        }
        else {
            label.classList.add('required');
        }
    }
    else {
        console.log(`Pas de label pour ${field.id}`);
    }
}