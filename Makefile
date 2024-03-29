DOCKER_COMPOSE_FILE=./docker-compose.yml
DOCKER_COMPOSE_DIR=./.docker
DOCKER_COMPOSE=docker-compose --env-file $(DOCKER_COMPOSE_DIR)/.env -f $(DOCKER_COMPOSE_FILE)
TOPDIR=$(shell pwd)
USER_ID=$(shell id -u)

.PHONY: setup
setup: docker-init
	$(DOCKER_COMPOSE) run workspace composer install
	$(DOCKER_COMPOSE) run workspace phive install

build: docker-init
	$(DOCKER_COMPOSE) build

workspace: docker-init
	$(DOCKER_COMPOSE) run workspace sh

.PHONY: analyse
analyse:
	$(DOCKER_COMPOSE) run workspace psalm

.PHONY: test
test:
	$(DOCKER_COMPOSE) run workspace phpunit

docs:
	docker run --rm --volume $(TOPDIR):/data phpdoc/phpdoc:3 project:run

release-major:
	$(DOCKER_COMPOSE) run workspace monorepo-builder release major

release-minor:
	$(DOCKER_COMPOSE) run workspace monorepo-builder release minor

release-patch:
	$(DOCKER_COMPOSE) run workspace monorepo-builder release patch

.docker/.env:
	cp $(DOCKER_COMPOSE_DIR)/.env.example $(DOCKER_COMPOSE_DIR)/.env

.PHONY: docker-init
docker-init: .docker/.env
