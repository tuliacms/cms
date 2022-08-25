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

.PHONY: webpack-build-production
webpack-build-production:
	export NODE_ENV=production
	npm run --prefix src/Cms/TuliaEditor/Infrastructure/Framework/Resources/public/tulia-editor build
	npm run --prefix src/Cms/Filemanager/Infrastructure/Framework/Resources/public/filemanager build
	npm run --prefix src/Cms/Content/Type/Infrastructure/Framework/Resources/public/layout-builder build
	# && cd src/Cms/ContactForm/Infrastructure/Framework/Resources/public \
	# && gulp \
	export NODE_ENV=development
	cd ../ && make console assets:publish

.SILENT:
