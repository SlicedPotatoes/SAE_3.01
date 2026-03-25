export function setCardShow(value) {
    const date = new Date();
    date.setTime(date.getTime() + (24*60*60*1000));
    document.cookie = `cardOpen=${value}; expires=${date.toUTCString()};`;
}

export function setHideRuleModal(value) {
    const date = new Date();
    date.setTime(date.getTime() + (24*60*60*1000));
    document.cookie = `hideRuleModal=${value}; expires=${date.toUTCString()};`;
}