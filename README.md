# Prérequis

- Docker (Docker Desktop)

# Entrer dans un conteneur le terminal

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

Le mot de passe root est généré aléatoirement lors de la commande `docker compose up --wait` et lorsque le volume de la base de données est créé. Pour le récupérer, allez dans les logs du conteneur de la base de données.

Si vous avez perdu le mot de passe root, supprimez le volume de base de données et refaites l'étape précédente.

Ensuite allez dans le conteneur de la base de données puis faire la commande :

```
mariadb --user=root -p <<-EOSQL
    CREATE DATABASE IF NOT EXISTS app_test;
    GRANT ALL PRIVILEGES ON \`app_test%\`.* TO 'user'@'%';
EOSQL
```

Dans le conteneur php faire la commande : `php bin/console --env=test doctrine:schema:create`

# Lancer les tests

Dans le projet faire la commande `make test`.

# Commandes

Vous pouvez retrouver la liste des commandes possibles dans les fichiers Makefile et package.json. Par exemple :

- make php-linters
- make eslint
- npm run lint
- make js-build
- make test
