DOCKER_COMPOSE_CMD=docker-compose
COMPOSER_CMD=composer
SYMFONY_CMD=bin/console
PHP_CS_FIXER_CMD=vendor/bin/php-cs-fixer

dev-up:
	$(DOCKER_COMPOSE_CMD) build
	$(DOCKER_COMPOSE_CMD) up -d

dev-init:
	$(DOCKER_COMPOSE_CMD) exec banality-php $(COMPOSER_CMD) install
	$(DOCKER_COMPOSE_CMD) exec banality-php $(SYMFONY_CMD) doctrine:database:drop --force
	$(DOCKER_COMPOSE_CMD) exec banality-php $(SYMFONY_CMD) doctrine:database:create
	$(DOCKER_COMPOSE_CMD) exec banality-php $(SYMFONY_CMD) doctrine:schema:create
	$(DOCKER_COMPOSE_CMD) exec banality-php $(SYMFONY_CMD) doctrine:fixtures:load --no-interaction

dev-down:
	$(DOCKER_COMPOSE_CMD) down

dev-cli:
	$(DOCKER_COMPOSE_CMD) exec banality-php bash

php-cs-check:
	$(DOCKER_COMPOSE_CMD) exec -e PHP_CS_FIXER_FUTURE_MODE=1 banality-php $(PHP_CS_FIXER_CMD) fix --verbose --diff --dry-run

php-cs-fix:
	$(DOCKER_COMPOSE_CMD) exec -e PHP_CS_FIXER_FUTURE_MODE=1 banality-php $(PHP_CS_FIXER_CMD) fix

.PHONY: dev-up dev-init dev-down dev-cli php-cs-check php-cs-fix