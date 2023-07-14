UID := $(shell id -u)
GID := $(shell id -g)

export UID
export GID

DOCKER_CMD := docker-compose
ifeq ($(OS),Windows_NT)
  DOCKER_CMD := $(DOCKER_CMD) -f "%cd%\docker\docker-compose.yml"
else
  DOCKER_CMD := $(DOCKER_CMD) -f $$(pwd)/docker/docker-compose.yml
endif

PARAMS=$(filter-out $@,$(MAKECMDGOALS))

DOCKER_CMD_PHP_CLI := $(DOCKER_CMD) exec php-fpm

set-env:
	cp -v ./docker/.env.example ./docker/.env
nginx-console:
	$(DOCKER_CMD) exec nginx sh
mysql-console:
	$(DOCKER_CMD) exec mysql bash
php-console:
	$(DOCKER_CMD_PHP_CLI) bash
up:
	$(DOCKER_CMD) up
start:
	$(DOCKER_CMD) start
stop:
	$(DOCKER_CMD) stop
down:
	$(DOCKER_CMD) down --remove-orphans
rm:
	$(DOCKER_CMD) rm
build:
	$(DOCKER_CMD) up -d --force-recreate --build --remove-orphans
composer-install:
	$(DOCKER_CMD_PHP_CLI) composer install
composer-update:
	$(DOCKER_CMD_PHP_CLI) composer update
init-dev: set-env build composer-install
init-prod: build composer-install
php-tests:
	$(DOCKER_CMD_PHP_CLI) php vendor/bin/codecept run --steps
docker-logs:
	$(DOCKER_CMD) logs $(PARAMS)
docker-config:
	$(DOCKER_CMD) config
docker-add-user:
	sudo usermod -aG docker $(whoami)

%:
	@:
