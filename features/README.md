# Lancement des tests d'intégration

## Prérequis
- Avoir installé les dépendances du projet avec `composer install`.

## Lancer les tests

Depuis la racine du projet, exécutez les commandes suivantes :

```bash
cd docker/test
docker compose up --build
cd ../..
```

Pour exécuter les tests d'intégration, utilisez la commande suivante :

```bash
vendor/bin/behat
```

Pour exécuter les tests d'intégration, en génèrent un rapport HTML, utilisez la commande suivante :

```bash
vendor/bin/behat --format html --out=reports
```

## Nettoyage

Après l'exécution des tests, vous pouvez arrêter les conteneurs Docker et nettoyer les ressources utilisées avec la commande suivante :

```bash
cd docker/test
docker compose down
```