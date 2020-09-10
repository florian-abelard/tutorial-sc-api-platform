#------------------------------------------------------------------------------
# Composer Makefile
#------------------------------------------------------------------------------

COMPOSER_DOCKER_CMD = docker-compose -f ${DOCKER_COMPOSE_BUILDER_FILE} run -T --user ${USER_ID}:${GROUP_ID} composer composer ${1}

# Cli arguments
ifneq (,$(filter composer-require% composer-remove%, $(firstword $(MAKECMDGOALS))))
    COMPOSER_CLI_ARGS := $(wordlist 2,$(words $(MAKECMDGOALS)),$(MAKECMDGOALS))
    $(eval $(COMPOSER_CLI_ARGS):;@:)
endif

# Command arguments
ifeq ($(ENV), test)
    COMPOSER_ARGS=--no-interaction
endif

#------------------------------------------------------------------------------

composer-init:
	@mkdir -p ~/.cache/composer

composer-require: composer-init ##@composer add a new package
	$(call COMPOSER_DOCKER_CMD, require $(COMPOSER_CLI_ARGS))

composer-require-dev: composer-init ##@composer add a new package in require-dev
	$(call COMPOSER_DOCKER_CMD, require $(COMPOSER_CLI_ARGS) --dev)

composer-remove: composer-init ##@composer remove a package
	$(call COMPOSER_DOCKER_CMD, remove $(COMPOSER_CLI_ARGS))

composer-remove-dev: composer-init ##@composer remove a package
	$(call COMPOSER_DOCKER_CMD, remove $(COMPOSER_CLI_ARGS) --dev)

composer-install: composer-init ##@composer install composer dependencies
	$(call COMPOSER_DOCKER_CMD, install --no-progress --no-suggest --prefer-dist --optimize-autoloader ${COMPOSER_ARGS})

composer-update: composer-init ##@composer update composer dependencies
	$(call COMPOSER_DOCKER_CMD, update,)

composer-dump-autoload: composer-init ##@composer dump autoloading
	$(call COMPOSER_DOCKER_CMD, dump-autoload)

#------------------------------------------------------------------------------

clean-composer:##@composer delete vendor directory
	test ! -e vendor || rm -r vendor

#------------------------------------------------------------------------------

.PHONY: composer-install composer-update composer-dump-autoload clean-composer
