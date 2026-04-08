# Tests d'interface Selenium – SAE S4.01

---

> **IMPORTANT :** Les tests ont été prévus avec une base de données fixe. Si la base de données a été modifiée, les tests risquent d'échouer.
>
> Il est recommandé de suivre les indications du fichier `README.md` à la racine du projet afin de lancer le Docker en mode démo.

---

## Installation

### 1. Se placer dans le dossier des tests

```bash
cd tests/testInterface
```

### 2. Créer le virtual environment

Sur PyCharm : 
- Aller dans le menu : `File > Settings > Python > Interpreter` > `Add interpreter` > `Add local interpreter`
- Choisir l'interpréteur `Virtualenv`
- Location : `tests/testInterface/.venv`
- Valider


### 3. Installer les dépendances

```bash
pip install -r requirements.txt
```

---

#### Dans le cas où la commande `pip` ne fonctionne pas :

```bash
pip install pytest
pip install pytest-html
pip install selenium
```

---



## Lancer les tests

### Tous les tests (rapport HTML généré automatiquement dans `reports/`)

```bash
pytest
```

### Un seul fichier

```bash
pytest scripts/test_StudentProcessed.py
pytest scripts/test_StudentNotProcessed.py
pytest scripts/test_RPNotProcessed.py
pytest scripts/test_RPProcessed.py
```

---

## Rapport HTML

Le `conftest.py` génère automatiquement un rapport HTML dans `reports/` à chaque
exécution. Le nom du fichier inclut la date et l'heure :

```
reports/rapport_sae_20260407_1530.html
```

En cas d'échec d'un test, une capture d'écran du navigateur au moment de
l'erreur est automatiquement intégrée dans le rapport.

---

## Structure du projet

```
testInterface/
    ├── .venv/
    ├── conftest.py                 
    ├── requirements.txt
    ├── README.md
    ├── scripts/
    │   ├── test_StudentNotProcessed.py   # Étudiant \- justificatif en cours
    │   ├── test_StudentProcessed.py      # Étudiant \- justificatif traité
    │   ├── test_RPNotProcessed.py        # RP \- justificatif en cours (3 slides)
    │   └── test_RPProcessed.py           # RP \- justificatif traité
    └── reports/                         # Rapports HTML générés automatiquement
```

---

## Pages testées

| Fichier de test | Rôle | État | Slides |
|---|---|---|---|
| `test_StudentNotProcessed.py` | Étudiant | En cours | 1 + 2 |
| `test_StudentProcessed.py` | Étudiant | Traité | 1 + 2 |
| `test_RPNotProcessed.py` | Responsable péda. | En cours | 1 + 2 + 3 |
| `test_RPProcessed.py` | Responsable péda. | Traité | 1 + 2 |


