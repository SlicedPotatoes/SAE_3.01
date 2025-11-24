// FILE: public/script/verifPrerequiMDP.js
const formModifMDP = document.getElementById('formModifMDP');
const inputNewMDP = document.getElementById('inputNewMDP');
const inputConfirmMDP = document.getElementById('inputConfirmMDP');
const alertModifMDP = document.getElementById('alertModifMDP');

const items = {
    length: document.getElementById('req-length'),
    uppercase: document.getElementById('req-uppercase'),
    lowercase: document.getElementById('req-lowercase'),
    digit: document.getElementById('req-digit'),
    special: document.getElementById('req-special'),
    nospace: document.getElementById('req-nospace'),
    match: document.getElementById('req-match')
};

const patterns = {
    uppercase: /[A-Z]/,
    lowercase: /[a-z]/,
    digit: /[0-9]/,
    special: /[!@#$%^&*(),.?":{}|<>\[\]\/\-+_=;`~]/,
    nospace: /^\S*$/
};

function setState(el, ok) {
    if (!el) return;
    el.classList.toggle('text-success', ok);
    el.classList.toggle('text-danger', !ok);
}

function validateAll() {
    const newMDP = inputNewMDP.value || '';
    const confirmMDP = inputConfirmMDP.value || '';

    const okLength = newMDP.length >= 12 && newMDP.length <= 30;
    const okUpper = patterns.uppercase.test(newMDP);
    const okLower = patterns.lowercase.test(newMDP);
    const okDigit = patterns.digit.test(newMDP);
    const okSpecial = patterns.special.test(newMDP);
    const okNoSpace = patterns.nospace.test(newMDP);
    const okMatch = newMDP === confirmMDP && newMDP.length > 0;

    setState(items.length, okLength);
    setState(items.uppercase, okUpper);
    setState(items.lowercase, okLower);
    setState(items.digit, okDigit);
    setState(items.special, okSpecial);
    setState(items.nospace, okNoSpace);
    setState(items.match, okMatch);

    return {
        okLength, okUpper, okLower, okDigit, okSpecial, okNoSpace, okMatch
    };
}

// Mise à jour en direct
inputNewMDP.addEventListener('input', () => {
    validateAll();
    alertModifMDP.classList.add('d-none');
    alertModifMDP.textContent = '';
});
inputConfirmMDP.addEventListener('input', () => {
    validateAll();
});

// Validation finale à la soumission
formModifMDP.addEventListener('submit', (e) => {
    alertModifMDP.classList.add('d-none');
    alertModifMDP.textContent = '';

    const res = validateAll();
    const allOk = res.okLength && res.okUpper && res.okLower && res.okDigit && res.okSpecial && res.okNoSpace && res.okMatch;

    if (!allOk) {
        e.preventDefault();
        alertModifMDP.textContent = 'Le mot de passe ne respecte pas tous les pré-requis.';
        alertModifMDP.classList.remove('d-none');
    }
});
