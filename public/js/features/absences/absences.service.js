import { fetchAPI } from "../../core/api.js";

export function fetchAbsences(query = "") {
    const url = query ? "api/absences?" + query : "api/absences";
    return fetchAPI(url);
}

export function fetchLockedAbsences() {
    return fetchAPI("api/absences?lock=true");
}

export function fetchExamAbsences() {
    return fetchAPI("api/absences?examen=true");
}