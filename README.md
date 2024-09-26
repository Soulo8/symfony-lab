# Prérequis

- Docker (Docker Desktop)

# Installer le projet

À la racine du projet, faire la commande `docker compose up` pour créer les conteneurs du projet. Par la suite, vous pourrez démarrer les conteneurs via l'interface graphique de Docker Desktop.

## Créer la base de données de test

- Avec le terminal se connecter au conteneur MySQL `docker compose exec database sh`.
- Faire la commande `mysql -u root -pverySecret`.
- Faire les commandes :

```
GRANT ALL PRIVILEGES ON *.* TO 'app'@'%';
FLUSH PRIVILEGES;
```

- Avec le terminal se connecter au conteneur du projet `docker compose exec web zsh`.
- Faire les commandes `php bin/console --env=test doctrine:database:create` et `php bin/console --env=test doctrine:schema:create`.

# Commandes

Vous pouvez retrouver la liste des commandes possibles dans les fichiers Makefile et package.json. Par exemple :

- make php-linters
- make eslint
- npm run lint
- make js-build
- make test