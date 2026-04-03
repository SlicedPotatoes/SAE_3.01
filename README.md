# SAE_3.01_4.01

## Contributeur

- SlicedPotatoes  -> Masmejean Kevin
- Steenkiste      -> van Steenkiste Dimitri
- Pileaucou59     -> Pileaucou Louis
- Casi223         -> Godisiabois Isaac
- LPtornad0       -> Helin Esteban
- Valyn5          -> Dascotte Yann

## Déploiement (mode démo)

### Information

Le mode démo permet de lancé l'application avec des données crédibles, permettant de tester les différentes fonctionnalités.

### Prérequis

Avant de lancer le projet, assurez-vous d'avoir Docker d'installé sur votre machine.

### Lancer l'application

Depuis la racine du projet, exécutez :

```bash
cd docker/demo
docker compose up --build
```

### Accéder à l'application

Une fois lancée, l'application est accessible à l'adresse :

```
http://localhost:8000
```

### Relancer proprement

Si vous souhaitez repartir de l'état initialise (base de données réinitialisée) :

```bash
docker compose down -v
docker compose up --build
```

### Arrêter l'application

Si vous souhaitez arrêter l'application en conservant les données

```bash
docker compose down
```

### Supprimer l'intégralité des données de docker (conteneur éteint)

```bash
docker system prune -a --volumes -f
```

## Déploiement (mode test)

### Information

Le mode test permet d'avoir une base de données avec des données prédictive pour les tests unitaires et d'intégration.

### Prérequis

Avant de lancer le projet, assurez-vous d'avoir Docker d'installé sur votre machine.

### Lancer la base de données de test

Depuis la racine du projet, exécutez :

```bash
cd docker/test
docker compose up --build
```

### Accéder à la base de données

Une fois lancée, la base de données est accessible à l'adresse :

```
localhost:5433
```

### Arrêter la base de données

A la fin de vos tests, vous pouvez arrêter la base de données en exécutant :

```bash
docker compose down
```

Les données de la base de données de test ne sont pas persistantes, elles seront réinitialisées à chaque lancement du conteneur.