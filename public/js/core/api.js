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