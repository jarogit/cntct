include .env
UID:= id -u ${DOCKER_USER}
GID:= id -g ${DOCKER_USER}
export DOCKER_UID = $(shell $(UID))
export DOCKER_GID = $(shell $(GID))

up:
	docker-compose up -d
stop:
	docker-compose stop
build:
	docker-compose up -d --build --force-recreate
ps:
	docker-compose ps
cache:
	docker-compose exec cntct-php find /tmp/symfony-cache/ -type f -exec rm -f {} + ;
bash:
	docker-compose exec cntct-php bash
bashr:
	docker-compose exec --user root cntct-php bash
bashw:
	docker-compose exec cntct-nginx ash
log:
	tail -F ../var/log/php_system.log
logp:
	docker-compose logs cntct-php
logw:
	docker-compose logs cntct-nginx
test:
	docker-compose exec cntct-php php bin/phpunit
