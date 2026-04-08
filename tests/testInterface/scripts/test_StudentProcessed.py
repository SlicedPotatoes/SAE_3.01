import time
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC

# Configuration de l'URL de base (à adapter selon ton serveur local)
BASE_URL = "localhost:8000"

# Identifiants de l'étudiant
STUDENT_EMAIL    = "etu2@uphf.fr"
STUDENT_PASSWORD = "password"


def test_StudentProcessed(driver):
    """
    Scénario : Connexion d'un étudiant et consultation d'un justificatif TRAITÉ.

    Vérifie :
      - Slide 1 : badge "Traité", date de traitement visible, 3 colonnes (motif + fichiers +
        commentaire RP), boutons Accueil / Voir les cours concernés.
      - Slide 2 : colonne "État" présente, badges de décision affichés, aucun toggle
        d'édition (sécurité rôle étudiant), boutons Retour / Accueil.
    """

    # --- 1. CONNEXION ---
    driver.set_window_size(1920, 1080)
    driver.get(f"http://{BASE_URL}")

    driver.find_element(By.ID, "email").send_keys(STUDENT_EMAIL)
    driver.find_element(By.ID, "password").send_keys(STUDENT_PASSWORD)
    driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()

    WebDriverWait(driver, 5).until(EC.url_contains("profil-etudiant"))
    assert "Profil étudiant" in driver.title
    print("Assert réussi : Redirection vers la page de profil confirmée.")

    # --- 2. FERMETURE DE LA MODALE D'ACCUEIL ---
    time.sleep(1)
    btn_fermer = WebDriverWait(driver, 10).until(
        EC.element_to_be_clickable((By.CSS_SELECTOR, "button.btn.btn-outline-secondary.me-2"))
    )
    btn_fermer.click()
    print("Étape réussie : Modale d'accueil fermée.")

    # --- 3. ACCÈS AU DÉTAIL DU JUSTIFICATIF TRAITÉ (id=-2) ---
    driver.find_element(By.CSS_SELECTOR, "a[href*='detail-justification/-2']").click()

    WebDriverWait(driver, 10).until(EC.url_contains("detail-justification"))
    assert "Justificati" in driver.title
    print("Assert réussi : Page de détail du justificatif chargée.")

    # Vérification du message de bienvenue
    msg_bienvenue = driver.find_element(By.CSS_SELECTOR, "div.container.m-0").text
    assert "Bonjour" in msg_bienvenue
    print("Assert réussi : Message de bienvenue trouvé.")

    # --- 4. VÉRIFICATIONS : SLIDE 1 (JUSTIFICATIF TRAITÉ) ---

    # On vérifie que la slide 1 est bien celle affichée (avec la date de début)
    date_debut = driver.find_element(By.XPATH, "//strong[contains(text(), 'Date début')]")
    assert date_debut.is_displayed
    print("Assert réussi : Slide 1 affichée (date de début visible).")

    # A. Badge de statut "Traité"
    badge = driver.find_element(By.CSS_SELECTOR, "span.badge.rounded-pill")
    assert "Traité" in badge.text, f"Badge attendu : 'Traité', obtenu : '{badge.text}'"
    print("Assert réussi : Badge de statut 'Traité' visible (justificatif traité).")

    # B. "Date de traitement" doit être affichée
    slide1 = driver.find_element(By.CSS_SELECTOR, "div.slide")
    assert "Date de traitement" in slide1.text
    print("Assert réussi : 'Date de traitement' visible (justificatif traité).")

    # C. Bloc "Commentaire du responsable" présent
    commentaires_rp = driver.find_elements(
        By.XPATH, "//strong[contains(text(), 'Commentaire du responsable')]"
    )
    assert len(commentaires_rp) > 0
    print("Assert réussi : Bloc 'Commentaire du responsable' présent (Slide 1).")

    # D. Labels des boutons Slide 1
    btn_next = driver.find_element(By.CSS_SELECTOR, "button.next")
    btn_prev = driver.find_element(By.CSS_SELECTOR, "button.prev")

    assert "Accueil" in btn_prev.get_attribute("textContent")
    print("Assert réussi : Label 'Accueil' sur le bouton Précédent (Slide 1).")

    assert "Voir les cours concernés" in btn_next.get_attribute("textContent")
    print("Assert réussi : Label 'Voir les cours concernés' sur le bouton Suivant (Slide 1).")

    # --- 5. NAVIGATION VERS SLIDE 2 ---
    btn_next.click()
    time.sleep(0.6)  # Attente de la transition d'animation

    # On vérifie que c'est bien la slide 2 qui est affichée
    titre_slide2 = driver.find_element(By.XPATH, "//h4[contains(text(), 'Heure de cours concerné')]")
    assert titre_slide2.is_displayed()
    print("Étape réussie : Navigation vers Slide 2 confirmée.")

    # --- 6. VÉRIFICATIONS : SLIDE 2 (LISTE DES ABSENCES, TRAITÉ) ---

    en_tete = driver.find_element(By.CSS_SELECTOR, ".table-like .row-content.table-light")

    # A. Colonne "État" présente
    assert "État" in en_tete.get_attribute("textContent")
    print("Assert réussi : Colonne 'État' présente dans le tableau (justificatif traité).")

    # B. Colonne "Action" absente (réservée au RP non traité)
    assert "Action" not in en_tete.get_attribute("textContent")
    print("Assert réussi : Colonne 'Action' absente (vue étudiant).")

    # C. Présence des badges de décision sur les lignes (ex : "Validée")
    badges = driver.find_elements(By.CSS_SELECTOR, ".row-content .badge")
    assert len(badges) > 0
    print("Assert réussi : Badges de décision présents sur les lignes du tableau (Slide 2).")

    # D. Aucun outil d'édition (Toggle) visible → sécurité interface étudiant
    toggles_rp = driver.find_elements(By.CSS_SELECTOR, "label.toggle")
    assert len(toggles_rp) == 0
    print("Assert réussi : Aucun Toggle visible pour l'étudiant.")

    # E. Labels des boutons Slide 2
    tous_les_prev = driver.find_elements(By.CSS_SELECTOR, "button.prev")
    tous_les_next = driver.find_elements(By.CSS_SELECTOR, "button.next")

    # Slide 2 : les boutons sont les deuxièmes de la page (index [1])
    btn_prev = tous_les_prev[1]
    btn_next = tous_les_next[1]

    assert "Retour" in btn_prev.get_attribute("textContent")
    print("Assert réussi : Label 'Retour' sur le bouton Précédent (Slide 2).")

    assert "Accueil" in btn_next.get_attribute("textContent")
    print("Assert réussi : Label 'Accueil' sur le bouton Suivant (Slide 2).")

    # --- 7. RETOUR À L'ACCUEIL ---
    btn_fin = driver.find_element(By.CSS_SELECTOR, "button.next[data-action='backToHome']")
    btn_fin.click()
    WebDriverWait(driver, 5).until(EC.url_contains("profil-etudiant"))
    print("Test terminé : Retour au profil réussi. Scénario validé.")
