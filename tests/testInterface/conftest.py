import pytest
import os
from datetime import datetime
from selenium import webdriver
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By


# --- CONFIGURATION DU NAVIGATEUR ---

@pytest.fixture(scope="function")
def driver():
    """Fixture pour initialiser le navigateur avant chaque test."""
    options = Options()
    # Si tu veux utiliser Opera, décommente les lignes suivantes :
    # options.binary_location = r"C:\Path\To\opera.exe"

    options.add_experimental_option("prefs", {
        "credentials_enable_service": False,
        "profile.password_manager_enabled": False,
    })
    options.add_argument("--disable-features=PasswordLeakDetection")
    options.add_argument("--incognito")  # ← aucun profil = aucune mémoire du mot de passe
    driver = webdriver.Chrome(options=options)
    driver.implicitly_wait(10)

    yield driver

    driver.quit()


# --- CONFIGURATION AUTOMATIQUE DU RAPPORT (Lancement auto) ---

def pytest_configure(config):
    """
    Force les options du rapport HTML sans avoir à les taper dans le terminal.
    Le CSS sera intégré (self-contained) et le nom du fichier est personnalisé.
    """
    if not config.getoption("--html"):
        # On définit le chemin du rapport (crée un dossier reports s'il n'existe pas)
        base_dir = os.path.dirname(os.path.abspath(__file__))
        reports_dir = os.path.join(base_dir, "reports")
        os.makedirs(reports_dir, exist_ok=True)
        config.option.htmlpath = os.path.join(reports_dir, f"rapport_sae_{datetime.now().strftime('%Y%m%d_%H%M')}.html")
        config.option.self_contained_html = True


# --- GESTION DES SCREENSHOTS EN CAS D'ÉCHEC ---

@pytest.hookimpl(hookwrapper=True)
def pytest_runtest_makereport(item, call):
    outcome = yield
    report = outcome.get_result()
    extra = getattr(report, "extra", [])

    if report.when == "call" and report.failed:
        # TENTATIVE DE RÉCUPÉRATION DU PLUGIN (Nom corrigé)
        pytest_html = item.config.pluginmanager.getplugin("html")

        driver = item.funcargs.get("driver")

        # ON VÉRIFIE QUE LE PLUGIN ET LE DRIVER EXISTENT BIEN
        if pytest_html and driver:
            screenshot = driver.get_screenshot_as_base64()
            html = '<div><img src="data:image/png;base64,%s" alt="screenshot" style="width:304px;height:228px;" ' \
                   'onclick="window.open(this.src)" align="right"/></div>' % screenshot

            # Utilisation sécurisée de extras
            extra.append(pytest_html.extras.html(html))

    report.extra = extra


def pytest_html_report_title(report):
    report.title = "SAE - Rapport de Tests d'interface"
