import { putAPI } from "../../core/api.js";

export async function updateMailNotification(data) {
    return await putAPI("/api/mailAlert", data);
}