# Créer la base de données de test

- Avec le terminal se connecter au conteneur MySQL.
- Faire la commande `mysql -u root -pverySecret`.
- Faire les commandes :

```
GRANT ALL PRIVILEGES ON *.* TO 'app'@'%';
FLUSH PRIVILEGES;
```

- Avec le terminal se connecter au conteneur du projet.
- Faire la commande `php bin/console --env=test doctrine:database:create`.
- Faire la commande `php bin/console --env=test doctrine:schema:create`.