import { fetchAPI } from "../../core/api.js";

export function fetchJustifications(query = "") {
    const url = query ? "/api/justifications?" + query : "/api/justifications";
    return fetchAPI(url);
}

export function fetchJustificationsToDo(query = "") {
    const url = query ? "/api/justifications?filters[state]=NotProcessed&" + query : "/api/justifications?filters[state]=NotProcessed";
    return fetchAPI(url);
}

export function fetchJustificationsDone(query = "") {
    const url = query ? "/api/justifications?filters[state]=Processed&" + query : "/api/justifications?filters[state]=Processed";
    return fetchAPI(url);
}