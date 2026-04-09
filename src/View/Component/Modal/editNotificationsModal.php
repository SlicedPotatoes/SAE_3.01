<?php

/**
 * Cette modal est utiliser pour configurer les obtions de notifications par mail pour le Responsable Pédagogique et l'enseignant
 */

use Uphf\GestionAbsence\Model\AuthManager;
use Uphf\GestionAbsence\Model\Entity\Account\AccountType;

?>

<div class="modal fade" id="EditNotificationModal" tabindex="-1" aria-labelledby="EditNotificationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="EditNotificationModalLabel">Gérer les notifications par mail</h1>
                <!-- Bouton pour quitter le modal, pour plus de clarité pour l'user -->
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Cela permet de pouvoir rediger vers la bonne page par rapport au type de comte -->
            <form method="post"
                  id="EditNotificationForm"
            >
            <div class="modal-body">
                <div class="form-switch mb-2">
                    <input class="form-check-input"
                        id="notif1"
                        type="checkbox"
                        role="switch"
                        name="notifications[mailAlertTeacher]"
                        value="1"
                        <?= AuthManager::getNotificationMail('teacher') ? 'checked' : '' ?>>
                    <label class="form-check-label" for="notif1">
                        Notification des absences en examen justifiées
                    </label>
                </div>

                <?php if (AuthManager::isRole(AccountType::EducationalManager)) : ?>
                <div class="form-switch mb-2">
                    <input class="form-check-input"
                           id="notif2"
                           type="checkbox"
                           role="switch"
                           name="notifications[mailAlertEducationalManager]"
                           value="1"
                           <?= AuthManager::getNotificationMail('educationalManager') ? 'checked' : '' ?>>
                    <label class="form-check-label" for="notif2">
                        Notification d’absence prolongée d’un étudiant (> 1 semaine)
                    </label>
                </div>
                <?php endif; ?>
            </div>

            <div class="modal-footer">
                <!-- Encore un bouton pour quitter le modal, pour plus de clarité pour l'user -->
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <!-- button pour submit le formulaire -->
                <button id="editNotifSubmit"
                        type="submit"
                        class="btn btn-primary btn-uphf">Enregistrer</button>
            </div>

            </form>
        </div>
    </div>
</div>

<script type="module" src="/js/pages/editNotificationsModal.js"></script>