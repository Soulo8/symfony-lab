WEB_CONTAINER = web

sh:
	docker compose exec $(WEB_CONTAINER) zsh

php-cs-fixer:
	docker compose exec $(WEB_CONTAINER) ./vendor/bin/php-cs-fixer fix

phpstan:
	docker compose exec $(WEB_CONTAINER) ./vendor/bin/phpstan analyze

linters: php-cs-fixer phpstan
	docker compose exec $(WEB_CONTAINER) ./bin/console lint:yaml config --parse-tags
	docker compose exec $(WEB_CONTAINER) ./bin/console lint:twig templates
	docker compose exec $(WEB_CONTAINER) ./bin/console lint:xliff translations
	docker compose exec $(WEB_CONTAINER) ./bin/console lint:container --no-debug 
	docker compose exec $(WEB_CONTAINER) ./bin/console doctrine:schema:validate --skip-sync -vvv --no-interaction
	docker compose exec $(WEB_CONTAINER) composer validate --strict
test:
	docker compose exec $(WEB_CONTAINER) ./vendor/bin/phpunit