import time
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC

# Configuration de l'URL de base
BASE_URL = "localhost:8000"

# Credentials RP de test
RP_EMAIL    = "rp@uphf.fr"
RP_PASSWORD = "password"


def test_RPProcessed(driver):
    """
    Scénario : Connexion en tant que responsable pédagogique et consultation
    d'un justificatif TRAITÉ — lecture seule, 2 slides.

    Vérifie :
      - Slide 1 : badge "Traité", bouton "Profil de l'étudiant", titre avec nom
        de l'étudiant, "Date de traitement" visible, 3 colonnes (motif + fichiers +
        commentaire RP), boutons Accueil / Voir les cours concernés.
      - Slide 2 : colonne "État" présente avec badges de décision, colonne "Action"
        absente, aucun toggle d'édition (lecture seule), boutons Retour / Accueil.
      - Slide 3 absente (uniquement pour RP non traité).
    """

    # --- 1. CONNEXION RP ---
    driver.set_window_size(1920, 1080)
    driver.get(f"http://{BASE_URL}")

    driver.find_element(By.ID, "email").send_keys(RP_EMAIL)
    driver.find_element(By.ID, "password").send_keys(RP_PASSWORD)
    driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()

    # Adapter "profil-responsable" si l'URL de l'accueil RP est différente
    WebDriverWait(driver, 5).until(EC.url_contains("justifications"))
    print("Assert réussi : Redirection vers l'accueil du responsable pédagogique.")

    # --- 2. ONGLET "TRAITÉ" ---
    onglet_traite = WebDriverWait(driver, 5).until(
        EC.element_to_be_clickable(
            (By.ID, "proofDone-tab")
        )
    )
    onglet_traite.click()
    time.sleep(0.5)
    print("Étape réussie : Onglet 'traité' sélectionné.")

    # --- 3. ACCÈS AU JUSTIFICATIF TRAITÉ (id=2) ---
    driver.find_element(By.CSS_SELECTOR, "a[href*='detail-justification/2']").click()

    WebDriverWait(driver, 10).until(EC.url_contains("detail-justification"))
    assert "Justificatif" in driver.title
    print("Assert réussi : Page de détail du justificatif traité (RP) chargée.")

    # --- 4. VÉRIFICATIONS PHYSIQUES : SLIDE 1 (RP / TRAITÉ) ---

    # A. Badge de statut "Traité"
    badge = driver.find_element(By.CSS_SELECTOR, "span.badge.rounded-pill")
    assert "Traité" in badge.text, f"Badge attendu : 'Traité', obtenu : '{badge.text}'"
    print("Assert réussi : Badge de statut correct")

    # B. Bouton "Profil de l'étudiant" présent
    btn_profil = driver.find_elements(By.CSS_SELECTOR, "a.btn.btn-outline-uphf")
    assert len(btn_profil) > 0 and "Profil de l'étudiant" in btn_profil[0].text
    print("Assert réussi : Bouton 'Profil de l'étudiant' présent (vue RP).")

    # C. Titre contient le nom de l'étudiant
    titre = driver.find_element(By.CSS_SELECTOR, "h3").text
    assert "1" in titre or "Étudiant" in titre, (
        f"Nom de l'étudiant attendu dans le titre, obtenu : '{titre}'"
    )
    print("Assert réussi : Titre de la page contient le nom de l'étudiant.")

    # D. "Date de traitement" visible
    slide1 = driver.find_element(By.CSS_SELECTOR, "div.slide")
    assert "Date de traitement" in slide1.text
    print("Assert réussi : 'Date de traitement' visible (justificatif traité).")

    # E. Bloc "Commentaire du responsable" présent (3ème colonne, isProcessed = true)
    commentaires_rp = driver.find_elements(
        By.XPATH, "//strong[contains(text(), 'Commentaire du responsable')]"
    )
    assert len(commentaires_rp) > 0
    print("Assert réussi : Bloc 'Commentaire du responsable' présent (Slide 1, RP traité).")

    # F. 3 colonnes sur Slide 1 (motif + fichiers + commentaire RP)
    slide1_el = driver.find_elements(By.CSS_SELECTOR, "div.slide")[0]
    colonnes_slide1 = slide1_el.find_elements(By.CSS_SELECTOR, ".row > div[class*='col-12']")
    assert len(colonnes_slide1) == 3, (
        f"Nombre de colonnes attendu : 3, obtenu : {len(colonnes_slide1)}"
    )
    print(f"Assert réussi : {len(colonnes_slide1)} colonnes sur Slide 1 (attendu : 3).")

    # G. Labels des boutons Slide 1
    btn_next = driver.find_element(By.CSS_SELECTOR, "button.next")
    btn_prev = driver.find_element(By.CSS_SELECTOR, "button.prev")

    assert "Accueil" in btn_prev.get_attribute("textContent")
    print("Assert réussi : Label 'Accueil' sur le bouton Précédent (Slide 1).")

    assert "Voir les cours concernés" in btn_next.get_attribute("textContent")
    print("Assert réussi : Label 'Voir les cours concernés' sur le bouton Suivant (Slide 1).")

    # --- 5. NAVIGATION VERS SLIDE 2 ---
    btn_next.click()
    time.sleep(0.6)

    titre_slide2 = driver.find_element(By.XPATH, "//h4[contains(text(), 'Heure de cours concerné')]")
    assert titre_slide2.is_displayed()
    print("Étape réussie : Navigation vers Slide 2 confirmée.")

    # --- 6. VÉRIFICATIONS PHYSIQUES : SLIDE 2 (RP / TRAITÉ) ---

    en_tete = driver.find_element(By.CSS_SELECTOR, ".table-like .row-content.table-light")

    # A. Colonne "État" présente avec décisions finales
    assert "État" in en_tete.get_attribute("textContent")
    print("Assert réussi : Colonne 'État' présente (justificatif traité).")

    # B. Colonne "Action" absente (disparaît une fois le justificatif traité)
    assert "Action" not in en_tete.get_attribute("textContent")
    print("Assert réussi : Colonne 'Action' absente (plus d'édition possible sur traité).")

    # C. Badges de décision présents sur les lignes (ex : "Validée")
    badges = driver.find_elements(By.CSS_SELECTOR, ".row-content .badge")
    assert len(badges) > 0
    print("Assert réussi : Badges de décision présents dans le tableau (justificatif traité).")

    # D. Aucun toggle d'édition (justificatif figé)
    toggles_rp = driver.find_elements(By.CSS_SELECTOR, "label.toggle")
    assert len(toggles_rp) == 0
    print("Assert réussi : Aucun Toggle d'édition présent (justificatif traité = lecture seule).")

    # E. Slide 3 absente : nbSlides = 2 pour RP traité
    tous_les_next = driver.find_elements(By.CSS_SELECTOR, "button.next")
    btn_next_s2 = tous_les_next[1]
    assert "Accueil" in btn_next_s2.get_attribute("textContent"), (
        "Le bouton Suivant devrait être 'Accueil' et non 'Saisir un commentaire' (traité)."
    )
    print("Assert réussi : Bouton Suivant = 'Accueil' (Slide 3 absente, traité).")

    # F. Label du bouton Précédent Slide 2
    tous_les_prev = driver.find_elements(By.CSS_SELECTOR, "button.prev")
    btn_prev_s2 = tous_les_prev[1]
    assert "Retour" in btn_prev_s2.get_attribute("textContent")
    print("Assert réussi : Label 'Retour' sur le bouton Précédent (Slide 2).")

    # --- 7. RETOUR À L'ACCUEIL RP ---
    btn_fin = driver.find_element(By.CSS_SELECTOR, "button.next[data-action='backToHome']")
    btn_fin.click()
    WebDriverWait(driver, 5).until(EC.url_contains("justifications"))
    print("Test terminé : Retour à l'accueil RP réussi. Scénario validé.")
