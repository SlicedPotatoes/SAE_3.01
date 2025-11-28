/**
 * Script JS utilisé dans la page DetailJustificationViewModel
 *
 * Il permet de gérer l'affichage dynamique pour la validation/lock individuelle des absences
 */

const STATE_VALIDATED_JUSTIFICATION = 'Validated';
const STATE_REFUSED_JUSTIFICATION = 'Refused';

// Récupération de la textaréa permettant au RP de mettre un commentaire
const commentInput = document.getElementById('JustificationRejectionReason');

// Récupération de la div qui contient les CTA des absences
const allAbsencesForJustification = document.querySelectorAll('.absence-cta-rp');

// Fonction qui permet le changement d'état d'une absence par le RP
function changeState(btn_state, btn_lock, input_state, newState) {
    if(newState === STATE_REFUSED_JUSTIFICATION) {
        btn_state.classList.replace('btn-outline-success', 'btn-outline-danger');
        btn_state.classList.remove('rounded');
        btn_state.textContent = (input_state ? 'R' : 'Tout r' ) + 'efusé';

        if (input_state) input_state.value = STATE_REFUSED_JUSTIFICATION;
        btn_lock.classList.replace('d-none', 'd-block');
    }
    else {
        btn_state.classList.replace('btn-outline-danger', 'btn-outline-success');
        btn_state.classList.add('rounded');
        btn_state.textContent = (input_state ? 'V' : 'Tout v' ) + 'alidé';

        if (input_state) input_state.value = STATE_VALIDATED_JUSTIFICATION;
        btn_lock.classList.replace('d-block', 'd-none');
    }
}
// Fonction qui permet le changement d'autorisé la re justification d'une absence par le RP
function changeLock(btn_lock, input_lock, new_lock) {
    console.log(btn_lock.value, new_lock);

    if(new_lock) {
        btn_lock.classList.replace('btn-success', 'btn-danger');
        btn_lock.innerHTML = '<i class="bi bi-lock"></i>';

        if(input_lock) input_lock.value = true;
    }
    else {
        btn_lock.classList.replace('btn-danger', 'btn-success');
        btn_lock.innerHTML = '<i class="bi bi-unlock"></i>';

        if(input_lock) input_lock.value = false;
    }
}

// Récupération de la div des CTA "ChangeAll"
const cta_rp_absence_div = document.getElementById('absence-cta-rp-all');
const btnChangeAllState = cta_rp_absence_div.firstElementChild;
const btnChangeAllLock = cta_rp_absence_div.lastElementChild;

// Gestion du click sur le bouton ChangeAllState
btnChangeAllState.addEventListener('click', () => {
    const newState = btnChangeAllState.classList.contains('btn-outline-danger') ? STATE_VALIDATED_JUSTIFICATION : STATE_REFUSED_JUSTIFICATION;

    changeState(btnChangeAllState, btnChangeAllLock, null, newState);

    // Changer l'état de chaque absence
    allAbsencesForJustification.forEach(div => {
        const input_state = div.parentNode.querySelector('.absence-input-state');
        const btn_state = div.querySelector('.absence-btn-state');
        const btn_lock = div.querySelector('.absence-btn-lock');

        changeState(btn_state, btn_lock, input_state, newState);
    });
});

// Gestion du click sur le bouton ChangeAllLock
btnChangeAllLock.addEventListener('click', () => {
    const newLock = btnChangeAllLock.classList.contains('btn-success');

    changeLock(btnChangeAllLock, null, newLock);

    // Changer le lock de chaque absence
    allAbsencesForJustification.forEach(div => {
        const btn_lock = div.querySelector('.absence-btn-lock');
        const input_lock = div.parentNode.querySelector('.absence-input-lock');

        changeLock(btn_lock, input_lock, newLock);
    });
});

allAbsencesForJustification.forEach(div => {
    const input_state = div.parentNode.querySelector('.absence-input-state');
    const input_lock = div.parentNode.querySelector('.absence-input-lock');
    const btn_state = div.querySelector('.absence-btn-state');
    const btn_lock = div.querySelector('.absence-btn-lock');

    // Gestion des clicks individuel pour l'état d'une absence
    btn_state.addEventListener('click', () => {
        if(input_state.value === STATE_REFUSED_JUSTIFICATION) {
            changeState(btn_state, btn_lock, input_state, STATE_VALIDATED_JUSTIFICATION);
        }
        else {
            changeState(btn_state, btn_lock, input_state, STATE_REFUSED_JUSTIFICATION);
        }
        commentInput.setCustomValidity("");
    });

    // Gestion des clicks individuel pour le lock d'une absence
    btn_lock.addEventListener('click', () => {
        changeLock(btn_lock, input_lock, input_lock.value === 'false');
    });
});

const formProcessJustification = document.getElementById('validateJustificationForm');

if(formProcessJustification) {
    formProcessJustification.addEventListener('submit', event => {
        console.log('UwU')
        event.preventDefault();

        let haveRefusedAbs = false;
        allAbsencesForJustification.forEach(div => {
            const input_state = div.parentNode.querySelector('.absence-input-state');

            if(input_state.value === STATE_REFUSED_JUSTIFICATION) {
                haveRefusedAbs = true;
            }
        });

        console.log(commentInput.value);

        if(haveRefusedAbs && commentInput.value.trim() === '') {
            commentInput.setCustomValidity("Ce champ est nécessaire en raison du refus d'au moins une absence");
            commentInput.reportValidity();
            return;
        }

        formProcessJustification.submit();
    });
}

if(commentInput) {
    commentInput.addEventListener('change', () => {
        commentInput.setCustomValidity('');
    });
}

// Gestion des commentaires prédéfinis
const predefinedComments = document.querySelectorAll('.predefined-comment');
predefinedComments.forEach(commentLink => {
    commentLink.addEventListener('click', (event) => {
        event.preventDefault();

        const commentText = commentLink.getAttribute('data-value');

        if (commentInput) {
            if (commentInput.value.trim() === '') {
                commentInput.value = commentText;
            } else {
                commentInput.value += '\n' + commentText;
            }

            commentInput.setCustomValidity('');
        }
    });
});