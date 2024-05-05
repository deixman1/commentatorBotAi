UID := $(shell id -u)
GID := $(shell id -g)
CUR_DIR:=$(shell dirname "$(realpath $(firstword $(MAKEFILE_LIST)))")

export UID
export GID
export CUR_DIR

DOCKER_CMD := docker-compose --env-file $(CUR_DIR)/docker/.env -f $(CUR_DIR)/docker/docker-compose.yml

PARAMS=$(filter-out $@,$(MAKECMDGOALS))

DOCKER_CMD_PHP_CLI := $(DOCKER_CMD) exec php-fpm
DOCKER_CMD_PHP_CLI_D := $(DOCKER_CMD) exec -d php-fpm

set-env:
	cp -v ./docker/.env.example ./docker/.env && cp -v ./.env.example ./.env
nginx-console:
	$(DOCKER_CMD) exec nginx sh
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
	$(DOCKER_CMD_PHP_CLI) bash -c "vendor/bin/codecept run `echo $(PARAMS)`"
docker-logs:
	$(DOCKER_CMD) logs $(PARAMS)
docker-config:
	$(DOCKER_CMD) config
#need reboot system
docker-add-user:
	sudo usermod -aG docker $(whoami)
set-webhook:
	curl https://api.telegram.org/bot$(PARAMS)/setWebhook?url=https://commentator-bot.loca.lt/telegram-webhook

make-command:
	$(DOCKER_CMD_PHP_CLI) php artisan make:command App\\Shared\\Infrastructure\\Console\\$(PARAMS)
make-job:
	$(DOCKER_CMD_PHP_CLI) php artisan make:job App\\Shared\\Job\\$(PARAMS)
queue-work:
	$(DOCKER_CMD_PHP_CLI) php artisan queue:work
queue-work-d:
	$(DOCKER_CMD_PHP_CLI_D) bash -c "php artisan queue:work >> /var/www/html/storage/logs/vk_processing.log"

%:
	@:
