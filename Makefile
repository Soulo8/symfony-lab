sh:
	docker compose exec php zsh

php-cs-fixer:
	docker compose exec php ./vendor/bin/php-cs-fixer fix

phpstan:
	docker compose exec php ./vendor/bin/phpstan analyze

phpinsights:
	docker compose exec php ./vendor/bin/phpinsights

php-linters: php-cs-fixer phpstan
	docker compose exec php ./vendor/bin/phpinsights --no-interaction
	docker compose exec php ./bin/console lint:yaml config --parse-tags
	docker compose exec php ./bin/console lint:twig templates
	docker compose exec php ./bin/console lint:xliff translations
	docker compose exec php ./bin/console lint:container
	docker compose exec php ./bin/console doctrine:schema:validate --skip-sync -vvv --no-interaction
	docker compose exec php composer validate --strict

eslint:
	docker compose exec php npm run lint

js-build:
	docker compose exec php npm run build

test:
	docker compose exec php ./vendor/bin/phpunit

drop-test-db:
	docker compose exec php php bin/console --env=test --force doctrine:database:drop

create-test-db:
	docker compose exec php php bin/console --env=test doctrine:database:create
	docker compose exec php php bin/console --env=test doctrine:schema:create

composer-audit:
	docker compose exec php composer audit