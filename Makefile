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
	echo "Executing: \e[94mBuilding Webpack production for TuliaEditor:\e[0m"
	npm run --prefix src/Cms/TuliaEditor/Infrastructure/Framework/Resources/public/tulia-editor build
	echo "Executing: \e[94mBuilding Webpack production for Filemanager:\e[0m"
	npm run --prefix src/Cms/Filemanager/Infrastructure/Framework/Resources/public/filemanager build
	echo "Executing: \e[94mBuilding Webpack production for ContentType Layout Builder:\e[0m"
	npm run --prefix src/Cms/Content/Type/Infrastructure/Framework/Resources/public/layout-builder build
	echo "Executing: \e[94mBuilding Webpack production for ContentType Blocks Builder:\e[0m"
	npm run --prefix src/Cms/Content/Block/Infrastructure/Framework/Resources/public/content-blocks-builder build
	echo "Executing: \e[94mBuilding Webpack production for Backend Layout:\e[0m"
	npm run --prefix src/Cms/Platform/Infrastructure/Framework/Resources/public/backend-layout build
	echo "Executing: \e[94mBuilding Webpack production for Contact Forms:\e[0m"
	npm run --prefix src/Cms/ContactForm/Infrastructure/Framework/Resources/public/contact-form-builder build
	echo "Executing: \e[94mBuilding Webpack production for Frontend Toolbar:\e[0m"
	npm run --prefix src/Cms/FrontendToolbar/Framework/Resources/public/frontend-toolbar build
	export NODE_ENV=development
	cd ../ && make console assets:publish

.SILENT:
