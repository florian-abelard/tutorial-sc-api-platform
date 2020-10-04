#------------------------------------------------------------------------------
# PHPUnit Makefile
#------------------------------------------------------------------------------

PHPUNIT_DOCKER_CMD = docker-compose run -T --user ${USER_ID}:${GROUP_ID} php vendor/bin/simple-phpunit ${1}

#------------------------------------------------------------------------------

phpunit: db-init-for-test ##@phpunit launch PHPUnit tests
	$(call PHPUNIT_DOCKER_CMD, --verbose)

phpunit-install-env: ##@phpunit install PHPUnit in environment
	docker-compose -f ${DOCKER_COMPOSE_BUILDER_FILE} run -T --rm --user ${USER_ID}:${GROUP_ID} composer ./vendor/bin/simple-phpunit install

#------------------------------------------------------------------------------

clean-phpunit: ##@phpunit clean PHPUnit

#------------------------------------------------------------------------------

.PHONY: phpunit phpunit-install-env
