.PHONY: bash behat

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

.SILENT:
