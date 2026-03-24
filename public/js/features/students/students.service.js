import { fetchAPI } from "../../core/api.js";

export function fetchStudents(query = "") {
    const url = query ? "/api/students?" + query : "/api/students";
    return fetchAPI(url);
}
