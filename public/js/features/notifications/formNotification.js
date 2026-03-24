import {updateMailNotification} from "./notifications.service.js";

export function initializeNotificationForm() {
    const form = document.getElementById("EditNotificationForm");
    const submitButton = document.getElementById("editNotifSubmit");

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        submitButton.disabled = true;

        const modal = bootstrap.Modal.getInstance(document.getElementById("EditNotificationModal"));

        const mailAlertTeacher = document.getElementById("notif1").checked;
        const mailAlertEducationalManager = document.getElementById("notif2") ? document.getElementById("notif2").checked : false;

        try {
            await updateMailNotification({
                mailAlertTeacher,
                mailAlertEducationalManager
            })

            submitButton.disabled = false;
            modal.hide();

        } catch (err) {
            console.error("ERREUR: ", err);
            submitButton.disabled = false;
        }
    });
}