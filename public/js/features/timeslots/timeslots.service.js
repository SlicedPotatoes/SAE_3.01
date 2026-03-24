import { fetchAPI } from "../../core/api.js";

export function fetchTimeSlots(query = "") {
    const url = query ? "/api/timeslots?" + query : "/api/timeslots";
    return fetchAPI(url);
}