sh:
	docker compose exec web zsh

php-cs-fixer:
	docker compose exec web ./vendor/bin/php-cs-fixer fix

phpstan:
	docker compose exec web ./vendor/bin/phpstan analyze

php-linters: php-cs-fixer phpstan
	docker compose exec web ./bin/console lint:yaml config --parse-tags
	docker compose exec web ./bin/console lint:twig templates
	docker compose exec web ./bin/console lint:xliff translations
	docker compose exec web ./bin/console lint:container
	docker compose exec web ./bin/console doctrine:schema:validate --skip-sync -vvv --no-interaction
	docker compose exec web composer validate --strict

eslint:
	docker compose exec web npm run lint

js-build:
	docker compose exec web npm run build

test:
	docker compose exec web ./vendor/bin/phpunit
