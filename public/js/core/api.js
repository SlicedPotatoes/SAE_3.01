// Fonction universelle pour utiliser les controllers API
export async function fetchAPI(url) {
    const response = await fetch(url);

    if (!response.ok) {
        throw Error(response.statusText);
    }

    const json = await response.json();
    return json.data || [];
}