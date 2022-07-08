.PHONY: bash behat deptrac

PHPROOT = docker exec -it --user "$(id -u):$(id -g)" --workdir="/var/www/html/core" tuliacms_tulia_www_1
ARGS = $(filter-out $@,$(MAKECMDGOALS))

.PHONY: bash
bash:
	${PHPROOT} /bin/bash

.PHONY: behat
behat:
	${PHPROOT} vendor/bin/behat "${ARGS}"

.PHONY: deptrac
deptrac:
	${PHPROOT} php deptrac.phar --fail-on-uncovered --report-uncovered

.PHONY: console
console:
	cd ../ && make console "$(ARGS)"

.PHONY: cc
cc:
	cd ../ && make cc

.PHONY: up
up:
	cd ../ && make up

.PHONY: down
down:
	cd ../ && make down

.SILENT:
