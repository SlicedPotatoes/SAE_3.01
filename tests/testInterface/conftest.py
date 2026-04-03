
import pytest
from selenium import webdriver

@pytest.fixture
def driver():
    # SETUP : On prépare le navigateur
    browser = webdriver.Chrome()
    yield browser # Le test s'exécute ici
    # TEARDOWN : On nettoie après le test
    browser.quit()

def test_ma_page_accueil(driver):
    driver.get("http://localhost:80")
    assert "Connexion" in driver.title

@pytest.hookimpl(hookwrapper=True)
def pytest_runtest_makereport(item, call):
    outcome = yield
    report = outcome.get_result()
    if report.when == 'call' and report.failed:
        driver = item.funcargs["driver"]
        screenshot = driver.get_screenshot_as_base64()
