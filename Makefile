DOCKER_COMPOSE_CMD=docker-compose
COMPOSER_CMD=composer
SYMFONY_CMD=bin/console
SYMFONY_PHPUNIT=bin/phpunit
PHP_CS_FIXER_CMD=vendor/bin/php-cs-fixer

dev-up:
	$(DOCKER_COMPOSE_CMD) build
	$(DOCKER_COMPOSE_CMD) up -d

dev-init:
	$(DOCKER_COMPOSE_CMD) exec banality-php $(COMPOSER_CMD) install
	$(DOCKER_COMPOSE_CMD) exec banality-php $(SYMFONY_CMD) doctrine:database:drop --force
	$(DOCKER_COMPOSE_CMD) exec banality-php $(SYMFONY_CMD) doctrine:database:create
	$(DOCKER_COMPOSE_CMD) exec banality-php $(SYMFONY_CMD) doctrine:schema:create
	$(DOCKER_COMPOSE_CMD) exec banality-php $(SYMFONY_CMD) doctrine:migrations:sync-metadata-storage
	$(DOCKER_COMPOSE_CMD) exec banality-php $(SYMFONY_CMD) doctrine:migrations:version --add --all
	$(DOCKER_COMPOSE_CMD) exec banality-php $(SYMFONY_CMD) doctrine:fixtures:load --no-interaction

dev-down:
	$(DOCKER_COMPOSE_CMD) down

dev-stop:
	$(DOCKER_COMPOSE_CMD) stop

dev-cli:
	$(DOCKER_COMPOSE_CMD) exec banality-php bash

php-cs-check:
	$(DOCKER_COMPOSE_CMD) exec -e PHP_CS_FIXER_FUTURE_MODE=1 banality-php $(PHP_CS_FIXER_CMD) fix --verbose --diff --dry-run

php-cs-fix:
	$(DOCKER_COMPOSE_CMD) exec -e PHP_CS_FIXER_FUTURE_MODE=1 banality-php $(PHP_CS_FIXER_CMD) fix

php-unit:
	$(DOCKER_COMPOSE_CMD) exec banality-php $(SYMFONY_PHPUNIT) --verbose

php-unit-coverage:
	$(DOCKER_COMPOSE_CMD) exec -e XDEBUG_MODE=coverage banality-php $(SYMFONY_PHPUNIT) --verbose --coverage-text

dev-check: php-cs-check php-unit

.PHONY: dev-up dev-init dev-down dev-stop dev-cli php-cs-check php-cs-fix php-unit php-unit-coverage dev-check