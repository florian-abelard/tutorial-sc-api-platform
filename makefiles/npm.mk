#------------------------------------------------------------------------------
# NPM and Webpack Makefile
#------------------------------------------------------------------------------

NPM_DOCKER_CMD = docker-compose -f ${DOCKER_COMPOSE_BUILDER_FILE} run -T --user ${USER_ID}:${GROUP_ID} node npm ${1}

# Cli arguments
ifneq (,$(filter npm-install% npm-uninstall, $(firstword $(MAKECMDGOALS))))
    NPM_CLI_ARGS := $(wordlist 2,$(words $(MAKECMDGOALS)),$(MAKECMDGOALS))
    $(eval $(NPM_CLI_ARGS):;@:)
endif

#------------------------------------------------------------------------------

npm-init: 
	test -e ~/.npm || mkdir ~/.npm

npm-install: npm-init ##@npm install npm dependencies
	$(call NPM_DOCKER_CMD, install $(NPM_CLI_ARGS) --silent)

npm-install-dev: npm-init ##@npm install npm dependencies
	$(call NPM_DOCKER_CMD, install $(NPM_CLI_ARGS) --save-dev)

npm-uninstall: npm-init ##@npm uninstall npm dependencies
	$(call NPM_DOCKER_CMD, uninstall $(NPM_CLI_ARGS))

npm-update: npm-init ##@npm update npm dependencies
	$(call NPM_DOCKER_CMD, update)

#------------------------------------------------------------------------------

webpack-build: ##@npm build assets for development environment
	$(call NPM_DOCKER_CMD, run dev)

webpack-build-production: ##@npm build assets for production environment
	$(call NPM_DOCKER_CMD, run build)

webpack-watch: ##@npm run webpack watch
	$(call NPM_DOCKER_CMD, run watch)

#------------------------------------------------------------------------------

clean-npm: ##@npm clean npm dependencies
	test ! -e node_modules || rm -rf node_modules

clean-built-assets: ##@npm clean built assets
	test ! -e public/build || rm -rf public/build

#------------------------------------------------------------------------------

.PHONY: npm-install npm-install-dev npm-uninstall npm-update webpack-build webpack-build-production webpack-watch clean-npm clean-built-assets
