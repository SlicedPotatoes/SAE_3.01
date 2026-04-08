import re
import time
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC


# Configuration de l'URL de base
BASE_URL = "localhost:8000"

# Identifiants du responsable pédagogique
RP_EMAIL    = "rp@uphf.fr"
RP_PASSWORD = "password"


def test_RPNotProcessed(driver):
    """
    Scénario : Connexion en tant que responsable pédagogique, consultation et traitement
    d'un justificatif EN COURS (non traité) — parcours sur les 3 slides.

    Vérifie :
      - Slide 1 : badge "En cours", bouton "Profil de l'étudiant", titre avec nom
        de l'étudiant, absence de "Date de traitement" et de "Commentaire du responsable",
        boutons Accueil / Voir les cours concernés.
      - Slide 2 : colonne "Action" présente, toggles d'édition visibles, aucune
        colonne "État", boutons Retour / Saisir un commentaire.
      - Slide 3 : sélecteur de commentaire prédéfini, textarea, indicateur d'erreur
        caché si tout est validé, visible si au moins un refus et commentaire vide,
        commentaires prédéfinis fonctionnels.
    """

    # --- 1. CONNEXION RP ---
    driver.set_window_size(1920, 1080)
    driver.get(f"http://{BASE_URL}")

    driver.find_element(By.ID, "email").send_keys(RP_EMAIL)
    driver.find_element(By.ID, "password").send_keys(RP_PASSWORD)
    driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()

    WebDriverWait(driver, 5).until(EC.url_contains("justifications"))           # Attente de la redirection vers la page des justificatifs
    print("Étape réussie : Connexion RP et redirection vers la page des justificatifs confirmées.")

    # --- 2. ACCÈS AU JUSTIFICATIF NON TRAITÉ (id=-1) ---
    driver.find_element(By.CSS_SELECTOR, "a[href*='detail-justification/-1']").click()

    WebDriverWait(driver, 10).until(EC.url_contains("detail-justification"))
    assert "Justificatif" in driver.title
    print("Assert réussi : Page de détail du justificatif (RP) chargée.")

    # --- 3. VÉRIFICATIONS : SLIDE 1 (RP / NON TRAITÉ) ---

    # On vérifie que la slide 1 est bien celle affichée (avec la date de début)
    date_debut = driver.find_element(By.XPATH, "//strong[contains(text(), 'Date début')]")
    assert date_debut.is_displayed
    print("Assert réussi : Slide 1 affichée (date de début visible).")

    # A. Badge "En cours"
    badge = driver.find_element(By.CSS_SELECTOR, "span.badge.rounded-pill")
    assert "En cours" in badge.text, f"Badge attendu : 'En cours', obtenu : '{badge.text}'"
    print(f"Assert réussi : Badge de statut correct")

    # B. Bouton "Profil de l'étudiant" présent et visible (vue RP)
    btn_profil = driver.find_elements(By.CSS_SELECTOR, "a.btn.btn-outline-uphf")
    assert len(btn_profil) > 0 and "Profil de l'étudiant" in btn_profil[0].text
    print("Assert réussi : Bouton 'Profil de l'étudiant' présent (vue RP).")

    # C. Titre contient le nom de l'étudiant
    titre = driver.find_element(By.CSS_SELECTOR, "h3").text
    regex_pattern = r"Justificatif de [A-Za-zÀ-ÿ\s]+, du \d{2}/\d{2}/\d{4}"
    assert re.match(regex_pattern, titre)
    print("Assert réussi : Titre contient le nom de l'étudiant et la date de dépôt (format attendu).")

    # D. "Date de traitement" absente
    slide1 = driver.find_element(By.CSS_SELECTOR, "div.slide")
    assert "Date de traitement" not in slide1.text
    print("Assert réussi : 'Date de traitement' absente (justificatif en cours).")

    # E. Bloc "Commentaire du responsable" absent
    commentaires_rp = driver.find_elements(
        By.XPATH, "//strong[contains(text(), 'Commentaire du responsable')]"
    )
    assert len(commentaires_rp) == 0
    print("Assert réussi : Bloc 'Commentaire du responsable' absent (non traité).")

    # F. Labels des boutons Slide 1
    btn_next = driver.find_element(By.CSS_SELECTOR, "button.next")
    btn_prev = driver.find_element(By.CSS_SELECTOR, "button.prev")

    assert "Accueil" in btn_prev.get_attribute("textContent")
    print("Assert réussi : Label 'Accueil' sur le bouton Précédent (Slide 1).")

    assert "Voir les cours concernés" in btn_next.get_attribute("textContent")
    print("Assert réussi : Label 'Voir les cours concernés' sur le bouton Suivant (Slide 1).")

    # --- 4. NAVIGATION VERS SLIDE 2 ---
    btn_next.click()
    time.sleep(0.6)

    # On vérifie que c'est bien la slide 2 qui est affichée
    titre_slide2 = driver.find_element(By.XPATH, "//h4[contains(text(), 'Heure de cours concerné')]")
    assert titre_slide2.is_displayed()
    print("Étape réussie : Navigation vers Slide 2 confirmée.")

    # --- 5. VÉRIFICATIONS : SLIDE 2 (RP / NON TRAITÉ) ---

    en_tete = driver.find_element(By.CSS_SELECTOR, ".table-like .row-content.table-light")

    # A. Colonne "Action" présente
    assert "Action" in en_tete.get_attribute("textContent")
    print("Assert réussi : Colonne 'Action' présente dans le tableau (RP non traité).")

    # B. Colonne "État" absente
    assert "État" not in en_tete.get_attribute("textContent")
    print("Assert réussi : Colonne 'État' absente (justificatif non encore traité).")

    # C. Toggles d'édition présents sur les lignes du tableau (label.toggle)
    toggles_rp = driver.find_elements(By.CSS_SELECTOR, "label.toggle")
    assert len(toggles_rp) > 0
    print("Assert réussi : Toggles d'édition présents sur les lignes du tableau.")

    # D. Boutons "valider tout" et "refuser tout" dans l'en-tête de la colonne Action
    btn_valider_tout = driver.find_element(
        By.CSS_SELECTOR,
        ".table-like .row-content.table-light strong i.btn-success[data-update-all-action='state'][data-update-all-value='Validated']"
    )
    btn_refuser_tout = driver.find_element(
        By.CSS_SELECTOR,
        ".table-like .row-content.table-light strong i.btn-danger[data-update-all-action='state'][data-update-all-value='Refused']"
    )
    assert btn_valider_tout.is_displayed() and btn_refuser_tout.is_displayed()
    print("Assert réussi : Boutons 'Valider tout' et 'Refuser tout' présents dans l'en-tête.")

    # E. Cliquer "Valider tout" et vérifier que tous les toggles sont cochés (état Validé)
    btn_valider_tout.click()
    time.sleep(0.3)  # Attente de la mise à jour des toggles

    checkboxes = driver.find_elements(By.CSS_SELECTOR, "label.toggle input[type='checkbox']")
    assert all(cb.is_selected() for cb in checkboxes), (
        "Tous les toggles devraient être en état 'Validé' (cochés) après 'Valider tout'."
    )
    print("Assert réussi : Tous les toggles sont cochés (état Validé) après avoir cliqué 'Valider tout'.")

    # F. Labels des boutons Slide 2
    tous_les_prev = driver.find_elements(By.CSS_SELECTOR, "button.prev")
    tous_les_next = driver.find_elements(By.CSS_SELECTOR, "button.next")

    btn_prev_s2 = tous_les_prev[1]
    btn_next_s2 = tous_les_next[1]

    assert "Retour" in btn_prev_s2.get_attribute("textContent")
    print("Assert réussi : Label 'Retour' sur le bouton Précédent (Slide 2).")

    assert "Saisir un commentaire" in btn_next_s2.get_attribute("textContent")
    print("Assert réussi : Label 'Saisir un commentaire' sur le bouton Suivant (Slide 2).")

    # --- 6. NAVIGATION VERS SLIDE 3 ---
    btn_next_s2.click()
    time.sleep(0.6)
    print("Étape réussie : Navigation vers Slide 3 (formulaire de commentaire).")

    # --- 7. VÉRIFICATIONS : SLIDE 3 (FORMULAIRE COMMENTAIRE) ---

    # A. Présence du sélecteur de commentaire prédéfini
    select_predefined = driver.find_element(By.ID, "predefinedComments")
    assert select_predefined.is_displayed()
    print("Assert réussi : Sélecteur de commentaire prédéfini présent.")

    # B. Présence de la zone de saisie textarea
    textarea = driver.find_element(By.ID, "comment")
    assert textarea.is_displayed()
    print("Assert réussi : Zone de saisie du commentaire présente.")

    # C. L'indicateur d'erreur est caché quand tout est validé et commentaire vide
    indicateur = driver.find_element(By.ID, "requiredCommentIndicator")
    assert "d-none" in indicateur.get_attribute("class"), (
        "L'indicateur d'erreur devrait être caché (aucun refus)."
    )
    print("Assert réussi : Indicateur d'erreur caché (tout validé, commentaire non obligatoire).")

    # D. Labels des boutons Slide 3
    tous_les_prev = driver.find_elements(By.CSS_SELECTOR, "button.prev")
    tous_les_next = driver.find_elements(By.CSS_SELECTOR, "button.next")

    btn_prev_s3 = tous_les_prev[2]
    btn_next_s3 = tous_les_next[2]

    assert "Retour" in btn_prev_s3.get_attribute("textContent")
    print("Assert réussi : Label 'Retour' sur le bouton Précédent (Slide 3).")

    assert "Valider" in btn_next_s3.get_attribute("textContent")
    print("Assert réussi : Label 'Valider' sur le bouton Suivant (Slide 3).")

    # --- 8. TEST : INDICATEUR ROUGE EN CAS DE REFUS SANS COMMENTAIRE ---
    # Retour sur Slide 2 pour refuser une absence
    btn_prev_s3.click()
    time.sleep(0.6)
    print("Étape réussie : Retour sur Slide 2.")

    # Refus de la première absence via le toggle de la première ligne du tableau
    toggle_premiere_ligne = driver.find_element(By.CSS_SELECTOR, "label.toggle")
    toggle_premiere_ligne.click()
    time.sleep(0.3)
    print("Étape réussie : Première absence refusée via le toggle.")

    # Navigation vers Slide 3
    tous_les_next = driver.find_elements(By.CSS_SELECTOR, "button.next")
    tous_les_next[1].click()
    time.sleep(0.6)
    print("Étape réussie : Navigation vers Slide 3 après refus d'une absence.")

    # L'indicateur d'erreur doit maintenant être visible (refus sans commentaire)
    indicateur = driver.find_element(By.ID, "requiredCommentIndicator")
    assert "d-none" not in indicateur.get_attribute("class"), (
        "L'indicateur d'erreur devrait être visible (refus présent, commentaire vide)."
    )
    print("Assert réussi : Indicateur d'erreur visible, refus sans commentaire.")

    cadre_rouge = driver.find_element(By.CSS_SELECTOR, "textarea#comment:invalid")
    assert cadre_rouge.is_displayed()
    print("Assert réussi : Cadre rouge visible autour de la zone de commentaire (indication d'erreur).")

    # --- 9. TEST : SAISIE D'UN COMMENTAIRE DE REFUS ---
    textarea = driver.find_element(By.ID, "comment")
    textarea.send_keys("Justification du refus de l'absence.")

    assert textarea.get_attribute("value") != ""
    print("Assert réussi : Commentaire correctement saisi dans la zone de texte.")

    cadre_vert = driver.find_element(By.CSS_SELECTOR, "textarea#comment:valid")
    assert cadre_vert.is_displayed()
    print("Assert réussi : Cadre vert visible autour de la zone de commentaire après saisie d'un texte.")

    print("Test terminé : Scénario RP non traité validé.")