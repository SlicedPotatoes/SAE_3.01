# Lancement des tests d'intégration

## Prérequis
- Avoir installé les dépendances du projet avec `composer install`.

## Lancer les tests

Depuis la racine du projet, exécutez les commandes suivantes pour lancer les tests d'intégration :

```bash
cd docker/test
docker compose up --build
cd ../..
vendor/bin/behat
```

Pour générer un rapport HTML après l'exécution des tests, utilisez la commande suivante :

```bash
vendor/bin/behat --format html --out=reports
```

## Nettoyage

Après l'exécution des tests, vous pouvez arrêter les conteneurs Docker et nettoyer les ressources utilisées avec la commande suivante :

```bash
cd docker/test
docker compose down
```