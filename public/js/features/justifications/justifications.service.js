import { fetchAPI } from "../../core/api.js";

export function fetchJustifications(query = "") {
    const url = query ? "/api/justifications?" + query : "/api/justifications";
    return fetchAPI(url);
}

export function fetchJustificationsToDo() {
    return fetchAPI("api/justifications?status=todo");
}

export function fetchJustificationsDone() {
    return fetchAPI("api/justifications?status=done");
}