# Docker
DOCKER_PROJECT          := karma8
DOCKER_SERVER           := server
DOCKER_SERVER_USER      := karma8_user
DOCKER_DB               := db
DOCKER_DIR              := docker

# Database
DB_NAME                 := karma8_db
DB_USER                 := karma8_db_user
DB_PASSWORD             := secret

# Init Config Files
DOCKER_COMPOSE          := docker-compose.yml
SERVER_INSTALL          := server/script/install.sh
DB_INSTALL              := install.sh
DB_INIT_SQL             := db/sql/db_init.sql
DB_DUMP_SQL             := db_dump.sql
DB_UPDATE_SQL           := db_update.sql

# Service Logs
PHP_ERROR_LOG           := /var/log/php/error.log
MYSQL_LOG               := /var/log/mysql/error.log

up: up-services server-start-cron

up-services:
	docker container start $(DOCKER_SERVER); \
	docker container start $(DOCKER_DB);

up-build:
	cd $(DOCKER_DIR); \
    docker-compose -p $(DOCKER_PROJECT) -f $(DOCKER_COMPOSE) up --build -d --force-recreate;

down:
	docker container stop $(DOCKER_SERVER); \
	docker container stop $(DOCKER_DB)

sh-server:
	docker exec -it $(DOCKER_SERVER) /bin/bash

sh-db:
	docker exec -it $(DOCKER_DB) /bin/bash

log-server:
	docker logs -f $(DOCKER_SERVER)

log-db:
	docker logs -f $(DOCKER_DB)

log-php:
	docker exec $(DOCKER_SERVER) /usr/bin/tail -f $(PHP_ERROR_LOG)

log-mysql:
	docker exec $(DOCKER_DB) /usr/bin/tail -f $(MYSQL_LOG)

db-init:
	cd $(DOCKER_DIR); \
	cat $(DB_INIT_SQL) | docker exec -i $(DOCKER_DB) mysql -u root -p$(DB_PASSWORD)

db-dump:
	docker exec -it $(DOCKER_DB) sh -c " \
        cd /docker-entrypoint-initdb.d && \
        gunzip $(DB_DUMP_SQL).gz; \
        mysql --max_allowed_packet=2147483648 --verbose --force --wait --reconnect -u root -D $(DB_NAME) -p$(DB_PASSWORD) < $(DB_DUMP_SQL); \
        rm -rf $(DB_DUMP_SQL); \
    "

db-update:
	docker exec -it $(DOCKER_DB) sh -c " \
        cd /docker-entrypoint-initdb.d && \
        mysql -u root -D $(DB_NAME) -p$(DB_PASSWORD) < $(DB_UPDATE_SQL) \
    "

server-install:
	docker exec -it $(DOCKER_SERVER) sh -c " \
        cd /var/www/$(DOCKER_DIR) && \
        /bin/sh $(SERVER_INSTALL) \
    "

server-commit:
	docker ps | grep $(DOCKER_SERVER) | awk '{print $1" $(DOCKER_SERVER)"}' | xargs docker commit

server-start-cron:
	docker exec -it $(DOCKER_SERVER) /usr/sbin/cron

db-install:
	docker exec -it $(DOCKER_DB) sh -c " \
        cd /docker-entrypoint-initdb.d && \
        /bin/sh $(DB_INSTALL) \
    "

db-commit:
	docker ps | grep $(DOCKER_DB) | awk '{print $1" $(DOCKER_DB)"}' | xargs docker commit

add-hosts:
	sudo $(DOCKER_DIR)/server/script/add-hosts.sh

clean:
	sudo rm -rf $(DOCKER_DIR)/db/data

install: clean up-build server-install server-commit db-install db-init db-commit db-dump db-update server-start-cron

.PHONY: $(MAKECMDGOALS)