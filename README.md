# Prérequis

- Docker (Docker Desktop)

# Se connecter avec le terminal aux conteneurs

Vous pouvez le faire via Docker Desktop ou avec les commandes :
- `make sh` ou `docker compose exec web zsh`
- `docker compose exec database sh`

# Installer le projet

- À la racine du projet, faire la commande `docker compose up` pour créer les conteneurs du projet. Par la suite, vous pourrez démarrer les conteneurs via l'interface graphique de Docker Desktop.
- Dans le conteneur du projet faire les commandes :
    - `composer install`.
    - `php bin/console doctrine:migrations:migrate`
    - `php bin/console --env=test doctrine:schema:create`
    - `php bin/console doctrine:fixtures:load --group=dev`
    - `php bin/console importmap:install`
    - `npm install`
    - `php bin/console tailwind:build`

# Commandes

Vous pouvez retrouver la liste des commandes possibles dans les fichiers Makefile et package.json. Par exemple :

- make php-linters
- make eslint
- npm run lint
- make js-build
- make test
