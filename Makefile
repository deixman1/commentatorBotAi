UID := $(shell id -u)
GID := $(shell id -g)
CUR_DIR:=$(shell dirname "$(realpath $(firstword $(MAKEFILE_LIST)))")

export UID
export GID

DOCKER_CMD := docker-compose -f $(CUR_DIR)/docker/docker-compose.yml

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
init: build composer-install
test:
	$(DOCKER_CMD_PHP_CLI) php vendor/bin/codecept run --steps
docker-logs:
	$(DOCKER_CMD) logs $(PARAMS)
docker-config:
	$(DOCKER_CMD) config
docker-add-user:
	sudo usermod -aG docker $(whoami)
set-webhook:
	curl https://api.telegram.org/bot$(PARAMS)/setWebhook?url=https://commentator-bot.loca.lt/telegram-webhook
vk-consumer:
	$(DOCKER_CMD) exec -d php-fpm bash -c "php bin/console/console.php vk-consumer >> /var/www/html/logs/vk-consumer.log 2>&1"

%:
	@:
