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

// Fonction universelle pour utiliser les controllers API

// Fonction à utiliser pour les fetch (c-à-d aller chercher les données)
export async function fetchAPI(url) {
    const response = await fetch(url);

    if (!response.ok) {
        throw Error(response.statusText);
    }

    const json = await response.json();
    return json.data || [];
}

// Fonction à utiliser pour update les données dans la base de données
export async function putAPI(url, data) {
    const response = await fetch(url, {
        method: "PUT",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data),
    });

    if (!response.ok) {
        throw Error(response.statusText);
    }

    return null;
}

/**
 * Effectuer une requête en envoyant du JSON
 *
 * @param url
 * @param method
 * @param data
 * @returns {Promise<any|null>}
 */
export async function sendJsonAPI(url, method, data = null) {
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