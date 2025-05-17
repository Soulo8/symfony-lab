# Prérequis

- Docker (Docker Desktop)

# Se connecter avec le terminal aux conteneurs

Vous pouvez le faire via Docker Desktop ou avec les commandes :
- `make sh` ou `docker compose exec web sh`
- `docker compose exec database sh`

# Configuration Docker

https://github.com/dunglas/symfony-docker/tree/main

# Installer le projet

- Dans le dossier du projet faire les commandes :
    - `docker compose build --pull --no-cache`
    - `docker compose up --wait` ou démarrer les conteneurs via l'interface graphique de Docker Desktop.
- Dans le conteneur du projet faire les commandes :
    - `composer install`.
    - `php bin/console doctrine:migrations:migrate`
    - `php bin/console doctrine:fixtures:load --group=dev`
    - `npm install`
    - `php bin/console tailwind:build`

# Créer la base de données de test

La base de données de test est créée lors de la création du conteneur Docker de la base de données. Si vous avez supprimé la base de données de test, vous pouvez la créer avec `php bin/console --env=test doctrine:database:create`.

Une fois la base de données créée, faites la commande `php bin/console --env=test doctrine:schema:create`.

# Commandes

Vous pouvez retrouver la liste des commandes possibles dans les fichiers Makefile et package.json. Par exemple :

- make php-linters
- make eslint
- npm run lint
- make js-build
- make test
