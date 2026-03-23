/**
 * Définition class d'erreur pour différencier les erreurs HTTP et réseaux
 */
export class HttpError extends Error {
    constructor(status, body) {
        super(`HTTP ${status}`);
        this.status = status;
        this.body =  body;
    }
}

/**
 * Effectuer une requête en envoyant du JSON
 *
 * @param url
 * @param method
 * @param data
 * @returns {Promise<any|null>}
 */
export async function sendJsonAPI(url, method, data) {
    const options = {
        method: method,
        headers: {
            "Content-Type": "application/json"
        },
        body: data
    };

    let response;

    try {
        response = await fetch(url, options);
    }
    catch (e) {
        throw Error(e);
    }

    if(!response.ok) {
        let body = null;

        try {
            body = await response.json();
        }
        catch {}

        throw new HttpError(response.status, body);
    }

    if(response.status === 201 || response.status === 204) {
        return null;
    }

    return await response.json();
}