SHELL ?= /bin/bash
ARGS = $(filter-out $@,$(MAKECMDGOALS))
include .env
export $(shell env | sed 's/=.*//' | tr '\n' '|')

YELLOW=\033[0;33m
BLUE=\033[0;34m
BOLDGREEN=\033[1;32m

.SILENT: ;
.ONESHELL: ;
.NOTPARALLEL: ;
.EXPORT_ALL_VARIABLES: ;
Makefile: ;

.DEFAULT_GOAL := help

.PHONY: env
env:
	cp .env.example .env

.PHONY: psr12-fix
psr12-fix:
	php vendor/bin/phpcbf --standard=psr12 app -n tests -n

.PHONY: psr12-check
psr12-check:
	php vendor/bin/phpcs --standard=psr12 app -n tests -n

.PHONY: psalm
psalm:
	php vendor/bin/psalm --no-cache

.PHONY: up
up:
	docker-compose up -d --remove-orphans

.PHONY: up-all
up-all:
	docker-compose up -d

.PHONY: up-min
up-min:
	docker-compose up -d app nginx

.PHONY: ps
ps:
	docker-compose ps

.PHONY: build
build:
	docker-compose build app nginx

.PHONY: restart
restart:
	docker-compose stop && docker-compose up -d --remove-orphans

.PHONY: bash
bash:
	docker-compose exec app bash ${ARGS}

.PHONY: gitlab
gitlab:
	docker-compose exec app composer config --global --auth gitlab-token.gitlab.internal.digitex.fun ${GITLAB_TOKEN_RO} && echo "Token stored!"

.PHONY: install
install:
	docker-compose exec app composer install

.PHONY: stop
stop:
	docker-compose stop ${ARGS}

.PHONY: tests
tests:
	docker-compose exec app vendor/bin/codecept run

.PHONY: tests-build
tests-build:
	docker-compose exec app vendor/bin/codecept build

.PHONY: tests-run
tests-run: tests-build
	docker-compose exec app vendor/bin/codecept run ${ARGS}

.PHONY: tests-run
redoc-generate:
	docker-compose exec node redoc-cli bundle ./docs/openapi.yml --output ./resources/views/docs/documents.html

.PHONY: default
default: help

.PHONY: help
help: .title
	printf '\n'
	printf "${BOLDGREEN}Available targets:${NC}\n"
	printf '\n'
	printf "${BLUE}make help${NC}:        ${YELLOW}Show this help${NC}\n"
	printf "${BLUE}make env${NC}:         ${YELLOW}Create .env file from .env.example${NC}\n"
	printf "${BLUE}make psr12-check${NC}: ${YELLOW}Check code in app and tests directory according PSR12 standards${NC}\n"
	printf "${BLUE}make psr12-fix${NC}:   ${YELLOW}Fix code in app and tests directory according PSR12 standards${NC}\n"
	printf "${BLUE}make psalm${NC}:       ${YELLOW}Check code in app directory via psalm${NC}\n"
	printf "${BLUE}make up${NC}:          ${YELLOW}Create and start local application in detached mode (in the background)${NC}\n"
	printf "${BLUE}make restart${NC}:     ${YELLOW}Restart local application in detached mode (in the background)${NC}\n"
	printf "${BLUE}make bash${NC}:        ${YELLOW}Run bash in local app container${NC}\n"
	printf "${BLUE}make tests${NC}:       ${YELLOW}Run tests in tests container${NC}\n"
	printf "${BLUE}make tests-build${NC}: ${YELLOW}Run codecept build in app container${NC}\n"
	printf "${BLUE}make tests-run${NC}:   ${YELLOW}Run custom codecept test, for example: make tests-run {PATH_TO_FILE} ${NC}\n"
	printf "${BLUE}make redoc-generate${NC}:${YELLOW}Generate API Document${NC}\n"
	printf "${BLUE}make stop${NC}:        ${YELLOW}Stop container {name}. Without arg - stop all${NC}\n"
	printf '\n'

%:
	@:
