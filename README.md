Ce projet est une application web développée avec Symfony 7 et intégrant API Platform 4 et Tailwind CSS 4. Il inclut une gestion minimale de produits et de voitures, permettant des uploads d'images et utilisant un peu de React pour ordonner ces images. L’API a était développé pour une gestion de tag et est utilisée dans le projet `angular-lab`.

# Prérequis

- Docker (Docker Desktop)

# Entrer dans un conteneur avec le terminal

Vous pouvez le faire via Docker Desktop ou avec les commandes :
- `make sh` ou `docker compose exec php bash`
- `docker compose exec database bash`

# Configuration Docker

https://github.com/dunglas/symfony-docker/tree/main

# Récupérer le mot de passe de l'utilisateur root de MariaDB

Le mot de passe root est généré aléatoirement lors de la commande `docker compose up --wait` et lorsque le volume de la base de données est créé. Pour le récupérer, allez dans les logs du conteneur de la base de données.

Si vous avez perdu le mot de passe root, supprimez le volume de base de données (LES DONNÉES QUE CONTIENT LA BASE DE DONNÉES SERONT SUPPRIMÉES) et refaire la commande `docker compose up --wait`.

# Installer le projet

- Dans le dossier du projet, faire les commandes :
    - `docker compose build --pull --no-cache`
    - `docker compose up --wait` ou démarrer les conteneurs via l'interface graphique de Docker Desktop.
- Dans le conteneur du projet, faire les commandes :
    - Inutile de faire les commandes `composer install` et `php bin/console doctrine:migrations:migrate`, car elles sont lancées lors de la commande `docker compose up --wait`.
    - Optionnel : `php bin/console doctrine:fixtures:load --group=dev`
    - `npm install`
    - `php bin/console tailwind:build`

# Créer la base de données de test

- `php bin/console --env=test doctrine:database:create`
- `php bin/console --env=test doctrine:schema:create`

# Lancer les tests

Dans le projet, faire la commande `make test`.

# Commandes

Vous pouvez retrouver la liste des commandes possibles dans les fichiers Makefile et package.json. Par exemple :

- make php-linters
- make eslint
- npm run lint
- make js-build
- make test