#------------------------------------------------------------------------------
# PHPUnit Makefile
#------------------------------------------------------------------------------

PHPUNIT_DOCKER_CMD = docker-compose run -T --user ${USER_ID}:${GROUP_ID} php vendor/bin/simple-phpunit ${1}

# Cli arguments
ifneq (,$(filter phpunit-filter, $(firstword $(MAKECMDGOALS))))
    PHPUNIT_FILTER_ARG := $(wordlist 2,$(words $(MAKECMDGOALS)),$(MAKECMDGOALS))
    $(eval $(PHPUNIT_FILTER_ARG):;@:)
endif

#------------------------------------------------------------------------------

phpunit: db-init-for-test ##@phpunit launch PHPUnit tests
	$(call PHPUNIT_DOCKER_CMD, --verbose)

phpunit-filter: ##@phpunit launch PHPUnit command with the filter passed in argument
	$(call PHPUNIT_DOCKER_CMD, --filter=$(PHPUNIT_FILTER_ARG))

phpunit-install-env: ##@phpunit install PHPUnit in environment
	docker-compose -f ${DOCKER_COMPOSE_BUILDER_FILE} run -T --rm --user ${USER_ID}:${GROUP_ID} composer ./vendor/bin/simple-phpunit install

#------------------------------------------------------------------------------

clean-phpunit: ##@phpunit clean PHPUnit

#------------------------------------------------------------------------------

.PHONY: phpunit phpunit-install-env
