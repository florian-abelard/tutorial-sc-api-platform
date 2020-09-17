#------------------------------------------------------------------------------
# Database Makefile
# 	- with doctrine
#------------------------------------------------------------------------------

database-admin-exec = docker-compose -f ${DOCKER_COMPOSE_FILE} exec -T --user root db ${1}
database-doctrine-exec = docker-compose -f ${DOCKER_COMPOSE_FILE} exec -T --user ${USER_ID} php ${1}

# Command arguments
ifeq ($(ENV), test)
    DB_ARGS=--env=test --no-interaction
endif

#------------------------------------------------------------------------------

db-init: db-create db-migrate db-populate ##@database create and populate database

db-init-for-test: db-create db-migrate db-populate

db-create: db-drop ##@database create the database
	$(call database-doctrine-exec, php bin/console doctrine:database:create $(DB_ARGS))

db-drop: ##@database drop the database
	$(call database-doctrine-exec, php bin/console doctrine:database:drop --if-exists --force $(DB_ARGS))

db-migrate: ##@database run the database migrations 
	$(call database-doctrine-exec, php bin/console doctrine:migrations:migrate $(DB_ARGS))

db-create-migration: ##@database create a new migration file
	$(call database-doctrine-exec, php bin/console make:migration)

db-populate: ##@database populate with fixtures data 
	$(call database-doctrine-exec, php bin/console hautelook:fixtures:load --no-bundles -v $(DB_ARGS))

db-schema-update: 
	$(call database-doctrine-exec, php bin/console doctrine:schema:update --force)

#------------------------------------------------------------------------------

clean-db: db-drop ##@database clean database

#------------------------------------------------------------------------------

.PHONY: db-init db-init-for-test db-create db-drop db-migrate db-create-migration db-populate db-schema-update clean-db
