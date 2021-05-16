DOCKER_COMPOSE_CMD=docker-compose
COMPOSER_CMD=composer
SYMFONY_CMD=bin/console
SYMFONY_PHPUNIT=bin/phpunit
PHP_CS_FIXER_CMD=vendor/bin/php-cs-fixer
CURRENT_UID := $(shell id -u)
CURRENT_GID := $(shell id -g)
BASE_DIR := $(shell pwd)
NODE_CMD=docker run -it --rm -w /var/src -v $(BASE_DIR)/app:/var/src node:16-alpine
export CURRENT_UID
export CURRENT_GID

dev-up:
	$(DOCKER_COMPOSE_CMD) build --build-arg UID=$(CURRENT_UID) --build-arg GID=$(CURRENT_GID)
	$(DOCKER_COMPOSE_CMD) up -d

dev-init:
	$(DOCKER_COMPOSE_CMD) exec banality-php $(COMPOSER_CMD) install
	$(DOCKER_COMPOSE_CMD) exec banality-php $(SYMFONY_CMD) doctrine:database:drop --force
	$(DOCKER_COMPOSE_CMD) exec banality-php $(SYMFONY_CMD) doctrine:database:create
	$(DOCKER_COMPOSE_CMD) exec banality-php $(SYMFONY_CMD) doctrine:schema:create
	$(DOCKER_COMPOSE_CMD) exec banality-php $(SYMFONY_CMD) doctrine:migrations:sync-metadata-storage
	$(DOCKER_COMPOSE_CMD) exec banality-php $(SYMFONY_CMD) doctrine:migrations:version --add --all --no-interaction
	$(DOCKER_COMPOSE_CMD) exec banality-php $(SYMFONY_CMD) doctrine:fixtures:load --no-interaction

dev-down:
	$(DOCKER_COMPOSE_CMD) down

dev-stop:
	$(DOCKER_COMPOSE_CMD) stop

dev-cli:
	$(DOCKER_COMPOSE_CMD) exec banality-php bash

cache-clear:
	$(DOCKER_COMPOSE_CMD) exec banality-php $(SYMFONY_CMD) cache:clear

frontend-dev:
	$(NODE_CMD) yarn install
	$(NODE_CMD) yarn encore dev

frontend-prod:
	$(NODE_CMD) yarn install
	$(NODE_CMD) yarn encore prod

frontend-update:
	$(NODE_CMD) yarn upgrade

frontend-cli:
	$(NODE_CMD) sh

dev-watch:
	$(NODE_CMD) yarn watch

php-cs-check:
	$(DOCKER_COMPOSE_CMD) exec -e PHP_CS_FIXER_FUTURE_MODE=1 banality-php $(PHP_CS_FIXER_CMD) fix --verbose --diff --dry-run

php-cs-fix:
	$(DOCKER_COMPOSE_CMD) exec -e PHP_CS_FIXER_FUTURE_MODE=1 banality-php $(PHP_CS_FIXER_CMD) fix

php-unit:
	$(DOCKER_COMPOSE_CMD) exec banality-php $(SYMFONY_PHPUNIT) --verbose

php-unit-coverage:
	$(DOCKER_COMPOSE_CMD) exec -e XDEBUG_MODE=coverage banality-php $(SYMFONY_PHPUNIT) --verbose --coverage-text

dev-check: php-cs-check php-unit

.PHONY: dev-up dev-init dev-down dev-stop dev-cli cache-clear frontend-dev frontend-prod frontend-update frontend-cli dev-watch php-cs-check php-cs-fix php-unit php-unit-coverage dev-check