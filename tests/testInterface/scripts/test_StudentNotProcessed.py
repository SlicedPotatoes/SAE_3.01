import re
import time
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC

# Configuration de l'URL de base (à adapter selon ton serveur local)
BASE_URL = "localhost:8000"

# Identifiants de l'étudiant
STUDENT_EMAIL    = "etu2@uphf.fr"
STUDENT_PASSWORD = "password"


def test_StudentNotProcessed(driver):
    """
    Scénario : Connexion d'un étudiant et consultation d'un justificatif EN COURS (non traité).

    Vérifie que l'interface est purement consultative :
      - Slide 1 : badge "En cours", pas de date de traitement, pas de colonne
        "Commentaire du responsable" (seulement 2 colonnes : motif + fichiers),
        boutons Accueil / Voir les cours concernés.
      - Slide 2 : pas de colonne "État" ni "Action", aucun badge de décision sur
        les lignes, aucun toggle d'édition, boutons Retour / Accueil.
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

    # --- 3. ACCÈS AU DÉTAIL DU JUSTIFICATIF NON TRAITÉ (id=-1) ---
    driver.find_element(By.CSS_SELECTOR, "a[href*='detail-justification/-1']").click()

    WebDriverWait(driver, 10).until(EC.url_contains("detail-justification"))
    assert "Justificati" in driver.title
    print("Assert réussi : Page de détail du justificatif chargée.")

    # Vérification du message de bienvenue
    msg_bienvenue = driver.find_element(By.CSS_SELECTOR, "div.container.m-0").text
    assert "Bonjour" in msg_bienvenue
    print("Assert réussi : Message de bienvenue trouvé.")

    # Vérification du titre (Date de dépôt)
    titre = driver.find_element(By.CSS_SELECTOR, "h3").text
    regex_pattern = r"Justificatif du \d{2}/\d{2}/\d{4}"
    assert re.match(regex_pattern, titre)
    print("Assert réussi : Titre de la page contient la date de dépôt du justificatif (format attendu).")

    # --- 4. VÉRIFICATIONS : SLIDE 1 (JUSTIFICATIF EN COURS) ---

    # On vérifie que la slide 1 est bien celle affichée (avec la date de début)
    date_debut = driver.find_element(By.XPATH, "//strong[contains(text(), 'Date début')]")
    assert date_debut.is_displayed
    print("Assert réussi : Slide 1 affichée (date de début visible).")

    # A. Badge de statut "En cours" (et non "Traité")
    badge = driver.find_element(By.CSS_SELECTOR, "span.badge.rounded-pill")
    assert "En cours" in badge.text, f"Badge attendu : 'En cours', obtenu : '{badge.text}'"
    print("Assert réussi : Badge de statut correct → 'En cours'.")

    # B. "Date de traitement" absente (le justificatif n'a pas encore été traité)
    slide1 = driver.find_element(By.CSS_SELECTOR, "div.slide")
    assert "Date de traitement" not in slide1.text
    print("Assert réussi : 'Date de traitement' absente (justificatif en cours).")

    # C. Bloc "Commentaire du responsable" absent
    commentaires_rp = driver.find_elements(
        By.XPATH, "//strong[contains(text(), 'Commentaire du responsable')]"
    )
    assert len(commentaires_rp) == 0
    print("Assert réussi : Bloc 'Commentaire du responsable' absent (justificatif non traité).")

    # D. Seulement 2 colonnes de contenu sur Slide 1 (motif + fichiers, sans commentaire RP)
    slide1_el = driver.find_elements(By.CSS_SELECTOR, "div.slide")[0]
    colonnes_slide1 = slide1_el.find_elements(By.CSS_SELECTOR, ".row > div[class*='col-12']")
    assert len(colonnes_slide1) == 2, (
        f"Nombre de colonnes attendu : 2, obtenu : {len(colonnes_slide1)}"
    )
    print("Assert réussi : Nombre de colonnes correct sur Slide 1 (2 colonnes : motif + fichiers).")

    # E. Labels des boutons Slide 1
    btn_next = driver.find_element(By.CSS_SELECTOR, "button.next")
    btn_prev = driver.find_element(By.CSS_SELECTOR, "button.prev")

    assert "Accueil" in btn_prev.get_attribute("textContent")
    print("Assert réussi : Label 'Accueil' sur le bouton Précédent (Slide 1).")

    assert "Voir les cours concernés" in btn_next.get_attribute("textContent")
    print("Assert réussi : Label 'Voir les cours concernés' sur le bouton Suivant (Slide 1).")

    # --- 5. NAVIGATION VERS SLIDE 2 ---
    btn_next.click()
    time.sleep(0.6)

    # On vérifie que c'est bien la slide 2 qui est affichée
    titre_slide2 = driver.find_element(By.XPATH, "//h4[contains(text(), 'Heure de cours concerné')]")
    assert titre_slide2.is_displayed()
    print("Étape réussie : Navigation vers Slide 2 confirmée.")

    # --- 6. VÉRIFICATIONS : SLIDE 2 (NON TRAITÉ, ÉTUDIANT) ---

    en_tete = driver.find_element(By.CSS_SELECTOR, ".table-like .row-content.table-light")

    # A. Colonne "État" absente
    assert "État" not in en_tete.get_attribute("textContent")
    print("Assert réussi : Colonne 'État' absente (justificatif non traité).")

    # B. Colonne "Action" absente (réservée au RP non traité)
    assert "Action" not in en_tete.get_attribute("textContent")
    print("Assert réussi : Colonne 'Action' absente.")

    # C. Aucun badge de décision (Validée/Refusée) sur les lignes
    badges_etat = driver.find_elements(By.CSS_SELECTOR, ".row-content .badge:not(.text-bg-warning)")
    assert len(badges_etat) == 0, (
        f"{len(badges_etat)} badge(s) d'état trouvé(s) — aucun attendu (justificatif non traité)."
    )
    print("Assert réussi : Aucun badge d'état (Validée/Refusée) dans le tableau.")

    # D. Aucun toggle d'édition (sécurité : étudiant ne peut pas modifier les états)
    toggles_rp = driver.find_elements(By.CSS_SELECTOR, "label.toggle")
    assert len(toggles_rp) == 0
    print("Assert réussi : Aucun Toggle visible pour l'étudiant.")

    # E. Labels des boutons Slide 2
    tous_les_prev = driver.find_elements(By.CSS_SELECTOR, "button.prev")
    tous_les_next = driver.find_elements(By.CSS_SELECTOR, "button.next")

    btn_prev = tous_les_prev[1]
    btn_next = tous_les_next[1]

    assert "Retour" in btn_prev.get_attribute("textContent")
    print("Assert réussi : Label 'Retour' sur le bouton Précédent (Slide 2).")

    assert "Accueil" in btn_next.get_attribute("textContent")
    print("Assert réussi : Label 'Accueil' sur le bouton Suivant (Slide 2).")

    # --- 7. TEST DE NAVIGATION RETOUR (Slide 2 → Slide 1) ---
    btn_prev.click()
    time.sleep(0.6)

    # On vérifie que la slide 1 est bien celle affichée (avec la date de début)
    date_debut = driver.find_element(By.XPATH, "//strong[contains(text(), 'Date début')]")
    assert date_debut.is_displayed
    print("Assert réussi : Retour sur la slide 1 confirmé")

    # --- 8. RETOUR À L'ACCUEIL ---
    btn_accueil = driver.find_element(By.CSS_SELECTOR, "button.prev[data-action='backToHome']")
    btn_accueil.click()
    WebDriverWait(driver, 5).until(EC.url_contains("profil-etudiant"))
    print("Test terminé : Retour au profil réussi. Scénario validé.")