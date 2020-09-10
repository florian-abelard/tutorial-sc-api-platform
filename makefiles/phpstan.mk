#------------------------------------------------------------------------------
# PHPStan Makefile
#------------------------------------------------------------------------------

PHPSTAN_DOCKER_CMD = docker-compose -f ${DOCKER_COMPOSE_ANALYSER_FILE} run -T phpstan ${1}

# Cli arguments
ifneq (,$(filter phpstan-analyse%, $(firstword $(MAKECMDGOALS))))
    COMPOSER_CLI_ARGS := $(wordlist 2,$(words $(MAKECMDGOALS)),$(MAKECMDGOALS))
    $(eval $(COMPOSER_CLI_ARGS):;@:)
endif

#------------------------------------------------------------------------------

phpstan-analyse: ##@phpstan run the analysis on the specified folder
	$(call PHPSTAN_DOCKER_CMD, analyse -c /app/docker/images/phpstan/phpstan.neon --level=6 $(COMPOSER_CLI_ARGS))

#------------------------------------------------------------------------------

.PHONY: phpstan-analyse
