Cette application Web a été développée avec Symfony 7, API Platform 4 et Tailwind CSS 4. L’API est utilisée par mon projet `angular-lab`.

# Prérequis

- Docker

# Entrer dans un conteneur

Vous pouvez le faire via Docker Desktop ou avec les commandes :
- `make sh` ou `docker compose exec php bash`
- `docker compose exec database bash`

# Configuration Docker

https://github.com/dunglas/symfony-docker/tree/main

# Récupérer le mot de passe de l'utilisateur root de MariaDB

Le mot de passe root est généré aléatoirement lors de la commande `docker compose up --wait` et lorsque le volume de la base de données est créé. Pour le récupérer, allez dans les logs du conteneur "database".

Si vous avez perdu le mot de passe root, supprimez le volume de base de données (LES DONNÉES QUE CONTIENT LA BASE DE DONNÉES SERONT SUPPRIMÉES) et refaire la commande `docker compose up --wait`.

# Installer le projet

- Dans le projet, en dehors d'un conteneur, faire les commandes :
    - `docker compose build --pull --no-cache`
    - `docker compose up --wait` ou démarrer les conteneurs via l'interface graphique de Docker Desktop.
- Dans le conteneur "php", faire les commandes :
    - Inutile de faire les commandes `composer install` et `php bin/console doctrine:migrations:migrate`, car elles sont lancées lors de la commande `docker compose up --wait`.
    - Optionnel : `php bin/console doctrine:fixtures:load --group=dev`
    - `npm install`
    - `php bin/console tailwind:build`

# Lancer les tests

Dans le projet, en dehors d'un conteneur, faire la commande `make test`.

# Commandes

Vous pouvez retrouver la liste des commandes possibles dans les fichiers Makefile et package.json. Par exemple :

- make php-linters
- make eslint
- npm run lint
- make js-build
- make test