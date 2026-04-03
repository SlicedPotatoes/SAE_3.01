from selenium import webdriver

# Initialise le driver
driver = webdriver.Chrome()

# Teste si ton serveur PHP répond (remplace par ton URL locale)
driver.get("http://localhost:80")

print("Le titre de la page est :", driver.title)

# Ferme le navigateur
driver.quit()